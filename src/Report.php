<?php

namespace Telebugs;

use Telebugs\Reporter;
use Telebugs\Backtrace;
use Telebugs\CodeHunk;
use Telebugs\Config;
use Telebugs\Truncator;

class Report implements \JsonSerializable
{
  const REPORTER = [
    'library' => ['name' => 'telebugs', 'version' => Reporter::VERSION],
    'platform' => ['name' => 'PHP', 'version' => PHP_VERSION]
  ];

  public array $data;

  public bool $ignored = false;

  private Config $config;

  // The maximum size of the JSON data in bytes
  private const MAX_REPORT_SIZE = 64000;

  // The maximum size of hashes, arrays and strings in the report.
  private const DATA_MAX_SIZE = 10000;

  private Truncator $truncator;

  public function __construct(\Throwable $e)
  {
    $this->config = Config::getInstance();
    $this->truncator = new Truncator(self::DATA_MAX_SIZE);
    $this->data = [
      'errors' => $this->errorsAsJson($e),
      'reporters' => [self::REPORTER]
    ];
  }

  private function errorsAsJson(\Throwable $e): array
  {
    $wrappedError = new WrappedError($e);
    return array_map(function ($e) {
      return [
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'backtrace' => $this->attachCode($e, Backtrace::parse($e)),
      ];
    }, $wrappedError->unwrap());
  }

  public function jsonSerialize(): mixed
  {
    return $this->ensureMaxReportSize($this->data);
  }

  private function ensureMaxReportSize(mixed $data): mixed
  {
    do {
      $truncatedData = $this->truncator->truncate($data);
      $json = json_encode($truncatedData);

      if ($json === false) {
        throw new \Exception('Failed to encode JSON');
      }

      if (strlen($json) <= self::MAX_REPORT_SIZE) {
        return $truncatedData;
      }
    } while ($this->truncator->reduceMaxSize() > 0);

    throw new \Exception('Failed to truncate report to acceptable size');
  }

  // @phpstan-ignore-next-line
  private function attachCode(\Throwable $e, array $backtrace): array
  {
    foreach ($backtrace as &$frame) {
      if (!isset($frame['file']) || !file_exists($frame['file'])) {
        continue;
      }
      if (!isset($frame['line'])) {
        continue;
      }
      if (!$this->frameBelongsToRootDirectory($frame['file'])) {
        continue;
      }
      if (preg_match('/vendor\/bundle/', $frame['file'])) {
        continue;
      }
      if (!is_readable($frame['file'])) {
        continue;
      }

      $frame['code'] = CodeHunk::get($frame['file'], $frame['line']);
    }

    // Exception object `getTrace` does not return file and line number for the first line
    // http://php.net/manual/en/exception.gettrace.php#107563
    array_unshift($backtrace, [
      'file' => $e->getFile(),
      'line' => $e->getLine(),
      'function' => '',
      'code' => CodeHunk::get($e->getFile(), $e->getLine()),
    ]);
    return $backtrace;
  }

  private function frameBelongsToRootDirectory(string $file): bool
  {
    $root = $this->config->getRootDirectory();
    return strpos($file, $root) === 0;
  }
}

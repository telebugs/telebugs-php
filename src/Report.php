<?php

namespace Telebugs;

use Telebugs\Reporter;
use Telebugs\Backtrace;

class Report
{
  const REPORTER = [
    'library' => ['name' => 'telebugs', 'version' => Reporter::VERSION],
    'platform' => ['name' => 'PHP', 'version' => PHP_VERSION]
  ];

  // @phpstan-ignore missingType.iterableValue
  public array $data;

  public bool $ignored = false;

  public function __construct(\Throwable $e)
  {
    $this->data = [
      'errors' => $this->errorsAsJson($e),
      'reporters' => [self::REPORTER]
    ];
  }

  // @phpstan-ignore missingType.iterableValue
  private function errorsAsJson(\Throwable $e): array
  {
    $wrappedError = new WrappedError($e);
    return array_map(function ($e) {
      return [
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'backtrace' => Backtrace::parse($e),
      ];
    }, $wrappedError->unwrap());
  }

  public function toJSON(): string
  {
    $json = json_encode($this->data);

    if ($json === FALSE) {
      throw new \Exception('Failed to encode JSON');
    }

    return $json;
  }
}

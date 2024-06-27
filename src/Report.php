<?php

namespace Telebugs;

use Telebugs\Reporter;

class Report
{
  const REPORTER = [
    'library' => ['name' => 'telebugs', 'version' => Reporter::VERSION],
    'platform' => ['name' => 'PHP', 'version' => PHP_VERSION]
  ];

  // @phpstan-ignore missingType.iterableValue
  public array $data;

  public bool $ignored = false;

  private \Throwable $error;

  public function __construct(\Throwable $e)
  {
    $this->error = $e;
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
        'backtrace' => []
      ];
    }, $wrappedError->unwrap());
  }

  public function toJSON(): string
  {
    $json = json_encode([
      'errors' => [
        [
          'type' => get_class($this->error),
          'message' => $this->error->getMessage(),
          'backtrace' => [
            [
              'file' => $this->error->getFile(),
              'line' => $this->error->getLine(),
              'function' => 'funcName'
            ]
          ],
        ]
      ]
    ]);

    if ($json === FALSE) {
      throw new \Exception('Failed to encode JSON');
    }

    return $json;
  }
}

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
        'backtrace' => []
      ];
    }, $wrappedError->unwrap());
  }
}

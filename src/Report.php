<?php

namespace Telebugs;

use Telebugs\Reporter;

class Report
{
  const REPORTER = [
    'library' => ['name' => 'telebugs', 'version' => Reporter::VERSION],
    'platform' => ['name' => 'PHP', 'version' => PHP_VERSION]
  ];

  public $data;

  public function __construct(\Throwable $e)
  {
    $this->data = [
      'errors' => $this->errorsAsJson($e),
      'reporters' => [self::REPORTER]
    ];
  }

  private function errorsAsJson(\Throwable $e): array
  {
    return [
      'type' => get_class($e),
      'message' => $e->getMessage(),
      'backtrace' => [],
    ];
  }
}

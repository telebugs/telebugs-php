<?php

declare(strict_types=1);

namespace Telebugs;

class Backtrace
{
  // @phpstan-ignore missingType.iterableValue
  public static function parse(\Throwable $e): array
  {
    $backtrace = $e->getTrace();
    $backtrace = array_map(function ($trace) {
      return [
        'file' => $trace['file'] ?? null,
        'line' => $trace['line'] ?? null,
        'function' => $trace['function']
      ];
    }, $backtrace);

    return $backtrace;
  }
}

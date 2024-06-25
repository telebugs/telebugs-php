<?php

namespace Telebugs;

class Reporter
{
  private static $instance;

  public static function getInstance(): Reporter
  {
    if (self::$instance === null) {
      self::$instance = new Reporter();
    }
    return self::$instance;
  }

  public function report(\Throwable $e): void
  {
    $className = get_class($e);
  }
}

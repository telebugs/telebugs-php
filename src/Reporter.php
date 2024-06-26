<?php

namespace Telebugs;

use Telebugs\Promise;

class Reporter
{
  public const VERSION = '0.1.0';

  private static $instance;

  public static function getInstance(): Reporter
  {
    if (self::$instance === null) {
      self::$instance = new Reporter();
    }
    return self::$instance;
  }

  private $sender;

  public function __construct()
  {
    $this->sender = new Sender();
  }

  public function report(\Throwable $e): Promise
  {
    return $this->sender->send(json_encode([
      'errors' => [
        [
          'type' => get_class($e),
          'message' => $e->getMessage(),
          'backtrace' => [
            [
              'file' => $e->getFile(),
              'line' => $e->getLine(),
              'function' => 'funcName'
            ]
          ],
        ]
      ]
    ]));
  }
}

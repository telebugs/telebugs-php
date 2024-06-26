<?php

namespace Telebugs;

use Telebugs\Promise;

class Reporter
{
  public const VERSION = '0.1.0';

  private static ?Reporter $instance = null;

  private Sender $sender;

  public static function getInstance(): Reporter
  {
    if (self::$instance === null) {
      self::$instance = new Reporter();
    }
    return self::$instance;
  }

  public function __construct()
  {
    $this->sender = new Sender();
  }

  public function report(\Throwable $e): Promise
  {
    $json = json_encode([
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
    ]);

    if ($json === FALSE) {
      throw new \Exception('Failed to encode JSON');
    }

    return $this->sender->send($json);
  }
}

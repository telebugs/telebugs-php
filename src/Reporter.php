<?php

namespace Telebugs;

use GuzzleHttp\Promise\FulfilledPromise;

use Telebugs\Promise;
use Telebugs\Sender;
use Telebugs\Config;

class Reporter
{
  public const VERSION = '0.1.0';

  private static ?Reporter $instance = null;

  private Sender $sender;
  private Config $config;

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
    $this->config = Config::getInstance();
  }

  public function report(\Throwable $e): Promise
  {
    $report = new Report($e);

    $this->config->middleware()($report);
    if ($report->ignored) {
      return new Promise(new FulfilledPromise("Report ignored"));
    }

    return $this->sender->send($report);
  }
}

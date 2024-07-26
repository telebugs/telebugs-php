<?php

declare(strict_types=1);

namespace Telebugs;

use Telebugs\Config;
use Telebugs\Reporter;

use GuzzleHttp\Promise\PromiseInterface;

function configure(\Closure $callback): void
{
  $callback(Config::getInstance());
}

function report(\Throwable $e): PromiseInterface
{
  return Reporter::getInstance()->report($e);
}

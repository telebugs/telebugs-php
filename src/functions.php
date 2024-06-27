<?php

declare(strict_types=1);

namespace Telebugs;

use Telebugs\Config;
use Telebugs\Reporter;
use Telebugs\Promise;

function configure(\Closure $callback): void
{
  $callback(Config::getInstance());
}

function report(\Throwable $e): Promise
{
  return Reporter::getInstance()->report($e);
}

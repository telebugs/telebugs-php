<?php

declare(strict_types=1);

namespace Telebugs;

use Telebugs\Config;
use Telebugs\Reporter;

function configure(array $options = []): void
{
  Config::getInstance()->configure($options);
}

function report(\Throwable $e): void
{
  Reporter::getInstance()->report($e);
}

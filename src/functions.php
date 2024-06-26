<?php

declare(strict_types=1);

namespace Telebugs;

use Telebugs\Config;
use Telebugs\Reporter;
use Telebugs\Promise;

/**
 * @param array<String, mixed> $options
 */
function configure(array $options = []): void
{
  Config::getInstance()->configure($options);
}

function report(\Throwable $e): Promise
{
  return Reporter::getInstance()->report($e);
}

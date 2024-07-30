<?php

declare(strict_types=1);

namespace Telebugs\Middleware;

use Telebugs\BaseMiddleware;

class IgnoreEnvironments extends BaseMiddleware
{
  private string $currentEnv;
  private array $ignoreEnvs;

  public function __construct(string $currentEnv, array $ignoreEnvs)
  {
    $this->currentEnv = $currentEnv;
    $this->ignoreEnvs = $ignoreEnvs;
  }

  public function __invoke($report): void
  {
    $report->ignored = in_array($this->currentEnv, $this->ignoreEnvs);
  }

  public function getWeight(): int
  {
    return -1000;
  }
}

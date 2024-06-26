<?php

namespace Telebugs;

use Telebugs\BaseMiddleware;
use Telebugs\Report;

class MiddlewareStack
{
  private $middlewares;

  public function __construct()
  {
    $this->middlewares = [];
  }

  public function use(BaseMiddleware $newMiddleware): void
  {
    $this->middlewares[] = $newMiddleware;
    usort($this->middlewares, function ($a, $b) {
      return $b->getWeight() - $a->getWeight();
    });
  }

  public function __invoke(Report $report): void
  {
    foreach ($this->middlewares as $middleware) {
      $middleware($report);
    }
  }

  public function delete(string $middlewareClass): void
  {
    $this->middlewares = array_filter($this->middlewares, function ($middleware) use ($middlewareClass) {
      return !($middleware instanceof $middlewareClass);
    });
  }

  public function getMiddlewares(): array
  {
    return $this->middlewares;
  }
}

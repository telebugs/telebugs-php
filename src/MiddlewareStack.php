<?php

namespace Telebugs;

use Telebugs\BaseMiddleware;
use Telebugs\Report;


class MiddlewareStack
{
  /**
   * @var BaseMiddleware[]
   */
  private array $middlewares;

  public function __construct()
  {
    $this->middlewares = [];
  }

  public function use(BaseMiddleware $newMiddleware): void
  {
    $this->middlewares[] = $newMiddleware;
    usort($this->middlewares, function (BaseMiddleware $a, BaseMiddleware $b) {
      $weightA = $a->getWeight();
      $weightB = $b->getWeight();

      if ($weightA === $weightB) {
        return 0;
      }
      return $weightA < $weightB ? -1 : 1;
    });
  }

  public function __invoke(Report $report): void
  {
    foreach ($this->middlewares as $middleware) {
      $middleware($report);
    }
  }

  public function delete(mixed $middlewareClass): void
  {
    $this->middlewares = array_values(array_filter($this->middlewares, function ($middleware) use ($middlewareClass) {
      return !($middleware instanceof $middlewareClass);
    }));
  }

  /**
   * @return BaseMiddleware[]
   */
  public function getMiddlewares(): array
  {
    return $this->middlewares;
  }
}

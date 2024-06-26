<?php

namespace Telebugs;

abstract class BaseMiddleware
{
  /**
   * Default weight for middleware. Can be overridden in child classes.
   *
   * @return int
   */
  public function getWeight(): int
  {
    return 0;
  }

  /**
   * This method should be implemented by all middleware classes.
   *
   * @param Report $report
   */
  abstract public function __invoke($report): void;
}

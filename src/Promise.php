<?php

namespace Telebugs;

use GuzzleHttp\Promise\PromiseInterface;

class Promise
{
  private PromiseInterface $promise;

  public function __construct(PromiseInterface $promise)
  {
    $this->promise = $promise;
  }

  // @phpstan-ignore missingType.parameter
  public function then(...$args): Promise
  {
    return new Promise($this->promise->then(...$args));
  }

  // @phpstan-ignore missingType.parameter
  public function otherwise(...$args): Promise
  {
    return new Promise($this->promise->otherwise(...$args));
  }

  // @phpstan-ignore missingType.parameter, missingType.return
  public function wait(...$args)
  {
    return $this->promise->wait(...$args);
  }

  public function getState(): string
  {
    return $this->promise->getState();
  }
}

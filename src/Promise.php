<?php

namespace Telebugs;

use GuzzleHttp\Promise\PromiseInterface;

class Promise
{
  private $promise;

  public function __construct(PromiseInterface $promise)
  {
    $this->promise = $promise;
  }

  public function then(...$args): Promise
  {
    return new Promise($this->promise->then(...$args));
  }

  public function otherwise(...$args): Promise
  {
    return new Promise($this->promise->otherwise(...$args));
  }

  public function wait(...$args): mixed
  {
    return $this->promise->wait(...$args);
  }

  public function getState(): string
  {
    return $this->promise->getState();
  }
}

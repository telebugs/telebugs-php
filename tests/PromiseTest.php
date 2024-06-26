<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Promise;

class PromiseTest extends TestCase
{
  public function testThen(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\FulfilledPromise(1));
    $newPromise = $promise->then(function ($value) {
      return $value + 1;
    });

    $this->assertEquals(2, $newPromise->wait());
  }

  public function testOtherwise(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\RejectedPromise(1));
    $newPromise = $promise->otherwise(function ($value) {
      return $value + 1;
    });

    $this->assertEquals(2, $newPromise->wait());
  }

  public function testWait(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\FulfilledPromise(1));

    $this->assertEquals(1, $promise->wait());
  }

  public function testGetState(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\FulfilledPromise(1));

    $this->assertEquals("fulfilled", $promise->getState());
  }
}

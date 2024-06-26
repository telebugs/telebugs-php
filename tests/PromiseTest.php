<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Promise;

class PromiseTest extends TestCase
{
  public function testThen(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\FulfilledPromise(['id' => 1]));
    $newPromise = $promise->then(function ($value) {
      $value['id']++;
      return $value;
    });

    $val = $newPromise->wait();
    $this->assertEquals(2, $val['id']);
  }

  public function testOtherwise(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\RejectedPromise(['id' => 1]));
    $newPromise = $promise->otherwise(function ($value) {
      $value['id']++;
      return $value;
    });

    $val = $newPromise->wait();
    $this->assertEquals(2, $val['id']);
  }

  public function testWait(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\FulfilledPromise(['id' => 1]));

    $val = $promise->wait();
    $this->assertEquals(1, $val['id']);
  }

  public function testGetState(): void
  {
    $promise = new Promise(new \GuzzleHttp\Promise\FulfilledPromise(1));

    $this->assertEquals("fulfilled", $promise->getState());
  }
}

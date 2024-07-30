<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;
use Telebugs\MiddlewareStack;
use Telebugs\Report;
use Telebugs\BaseMiddleware;

class TestFilteringMiddleware extends BaseMiddleware
{
  public function __invoke($report): void
  {
    $report->data['errors'] = [
      [
        "type" => "Exception",
        "message" => "[Filtered]",
      ]
    ];
  }

  public function getWeight(): int
  {
    return 1;
  }
}

class TestNewDataMiddleware extends BaseMiddleware
{
  public function __invoke($report): void
  {
    $report->data['new_data'] = [["data" => "new data"]];
  }

  public function getWeight(): int
  {
    return 10;
  }
}

class MiddlewareStackTest extends TestCase
{
  public function testInvoke(): void
  {
    $stack = new MiddlewareStack();
    $stack->use(new TestFilteringMiddleware());
    $stack->use(new TestNewDataMiddleware());

    $report = new Report(new \Exception("error message"));
    $stack($report);

    $this->assertEquals("[Filtered]", $report->data['errors'][0]['message']);
    $this->assertEquals("new data", $report->data['new_data'][0]['data']);
  }

  public function testWeight(): void
  {
    $stack = new MiddlewareStack();
    $stack->use(new TestFilteringMiddleware());
    $stack->use(new TestNewDataMiddleware());

    $middlewares = $stack->getMiddlewares();

    $this->assertEquals(2, count($middlewares));
    $this->assertInstanceOf(TestFilteringMiddleware::class, $middlewares[0]);
    $this->assertInstanceOf(TestNewDataMiddleware::class, $middlewares[1]);

    $stack = new MiddlewareStack();
    $stack->use(new TestNewDataMiddleware());
    $stack->use(new TestFilteringMiddleware());

    $middlewares = $stack->getMiddlewares();

    $this->assertEquals(2, count($middlewares));
    $this->assertInstanceOf(TestFilteringMiddleware::class, $middlewares[0]);
    $this->assertInstanceOf(TestNewDataMiddleware::class, $middlewares[1]);
  }

  public function testDelete(): void
  {
    $stack = new MiddlewareStack();
    $stack->use(new TestFilteringMiddleware());
    $stack->use(new TestNewDataMiddleware());

    $stack->delete(TestFilteringMiddleware::class);

    $middlewares = $stack->getMiddlewares();
    $this->assertCount(1, $middlewares);
    $this->assertInstanceOf(TestNewDataMiddleware::class, $middlewares[0]);
  }
}

<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;

use Telebugs\Config;
use Telebugs\BaseMiddleware;

use function Telebugs\configure;
use function Telebugs\report;

class TestIgnoreMiddleware extends BaseMiddleware
{
  public function __invoke($report): void
  {
    $report->ignored = true;
  }
}

class FunctionsTest extends TestCase
{
  private array $container; // @phpstan-ignore-line
  private $history; // @phpstan-ignore-line

  protected function setUp(): void
  {
    $this->container = [];
    $this->history = Middleware::history($this->container);
  }

  protected function tearDown(): void
  {
    configure(function ($config) {
      $config->reset();
    });
  }

  public function testConfigure(): void
  {
    configure(function ($config) {
      $config->setApiKey("999");
    });

    $this->assertEquals("999", Config::getInstance()->getApiKey());
  }

  public function testReport(): void
  {
    $mock = new MockHandler([
      new Response(201, [], '{"id":123}'),
    ]);
    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($this->history);

    configure(function ($config) use ($handlerStack) {
      $config->setHttpClient(new Client(['handler' => $handlerStack]));
    });

    $res = report(new \Exception("Test exception"))->wait();

    $this->assertEquals(123, $res['id']);
    $this->assertCount(1, $this->container);
  }

  public function testDoesNotReportIgnoredErrors(): void
  {
    $mock = new MockHandler([
      new Response(201, [], '{"id":123}'),
    ]);
    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push($this->history);

    configure(function ($config) use ($handlerStack) {
      $config->setHttpClient(new Client(['handler' => $handlerStack]));
      $config->middleware()->use(new TestIgnoreMiddleware());
    });

    report(new \Exception("Test exception"))->wait();

    $this->assertCount(0, $this->container);
  }
}

<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

use Telebugs\Config;

use function Telebugs\configure;
use function Telebugs\report;

class FunctionsTest extends TestCase
{
  protected function tearDown(): void
  {
    Config::getInstance()->reset();
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
    configure(function ($config) use ($mock) {
      $config->setHttpClient(new Client(['handler' => HandlerStack::create($mock)]));
    });

    $res = report(new \Exception("Test exception"))->wait();

    $this->assertEquals(123, $res['id']);
  }
}

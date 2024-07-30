<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Reporter;
use Telebugs\Config;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;

use function Telebugs\configure;

class ReporterTest extends TestCase
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
        Config::getInstance()->reset();
    }

    public function testReport(): void
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123}'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($this->history);

        configure(function ($config) use ($handlerStack) {
            $config->setApiKey("1:a0480999c3d12d13d4cdbadbf0fc2ba3");
            $config->setHttpClient(new Client(['handler' => $handlerStack]));
        });

        $reporter = new Reporter();
        $res = $reporter->report(new \Exception("Test exception"))->wait();

        $this->assertEquals(123, $res['id']);
        $this->assertCount(1, $this->container);
    }

    public function testReporterDoesNotReportIgnoredErrors(): void
    {
        $mock = new MockHandler([
            new Response(201, [], '{"id":123}'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($this->history);

        configure(function ($config) use ($handlerStack) {
            $config->setApiKey("1:a0480999c3d12d13d4cdbadbf0fc2ba3");
            $config->setHttpClient(new Client(['handler' => $handlerStack]));
            $config->middleware()->use(new TestIgnoreMiddleware());
        });

        $reporter = new Reporter();
        $reporter->report(new \Exception("Test exception"))->wait();

        $this->assertCount(0, $this->container);
    }
}

<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Reporter;
use Telebugs\Config;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;

use function Telebugs\configure;

class ReporterTest extends TestCase
{
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

        configure([
            'api_key' => "1:a0480999c3d12d13d4cdbadbf0fc2ba3",
            'http_client' => new Client(['handler' => $handlerStack]),
        ]);

        $reporter = new Reporter();
        $res = $reporter->report(new \Exception("Test exception"))->wait();

        $this->assertEquals(123, $res['id']);
    }
}

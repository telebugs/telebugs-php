<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Config;

class ConfigTest extends TestCase
{
  protected static Config $config;

  protected function setUp(): void
  {
    $this->config = new Config();
  }

  public function testSetApiKey(): void
  {
    $this->config->setApiKey("123456");
    $this->assertEquals("123456", $this->config->getApiKey());
  }

  public function testSetApiURL(): void
  {
    $this->config->setApiURL("https://api.telebugs.com/2024-03-28/errors");
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", $this->config->getApiURL());
  }

  public function testSetRootDirectory(): void
  {
    $this->config->setRootDirectory("/var/www/html");
    $this->assertEquals("/var/www/html", $this->config->getRootDirectory());
  }

  public function testConfigure(): void
  {
    $this->config->setApiKey("123456");
    $this->config->setApiURL("https://api.telebugs.com/2024-03-28/errors");
    $this->config->setRootDirectory("/var/www/html");

    $this->assertEquals("123456", $this->config->getApiKey());
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", $this->config->getApiURL());
    $this->assertEquals("/var/www/html", $this->config->getRootDirectory());
  }

  public function testSetHttpClient(): void
  {
    $this->config->setHttpClient(new \GuzzleHttp\Client());
    $this->assertInstanceOf(\GuzzleHttp\Client::class, $this->config->getHttpClient());
  }

  public function testMiddleware(): void
  {
    $this->config->middleware()->use(new TestIgnoreMiddleware());
    $this->assertEquals(1, count($this->config->middleware()->getMiddlewares()));
  }

  public function testReset(): void
  {
    $this->config->setApiKey("123456");
    $this->config->setApiURL("https://example.com");
    $this->config->setRootDirectory("/var/www/html");
    $this->config->middleware()->use(new TestIgnoreMiddleware());

    $this->config->reset();

    $this->assertEquals("", $this->config->getApiKey());
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", $this->config->getApiURL());
    $this->assertEquals("", $this->config->getRootDirectory());
    $this->assertEquals(0, count($this->config->middleware()->getMiddlewares()));
  }
}

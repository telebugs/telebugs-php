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
    if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
      $rootDirectory = realpath("D:\\");
    } else {
      $rootDirectory = realpath("/tmp");
    }
    $this->config->setRootDirectory($rootDirectory);
    $this->assertEquals($rootDirectory, $this->config->getRootDirectory());
  }

  public function testConfigure(): void
  {
    $this->config->setApiKey("123456");
    $this->config->setApiURL("https://api.telebugs.com/2024-03-28/errors");

    if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
      $rootDirectory = realpath("D:\\");
    } else {
      $rootDirectory = realpath("/tmp");
    }

    $this->config->setRootDirectory($rootDirectory);

    $this->assertEquals("123456", $this->config->getApiKey());
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", $this->config->getApiURL());
    $this->assertEquals($rootDirectory, $this->config->getRootDirectory());
  }

  public function testSetHttpClient(): void
  {
    $this->config->setHttpClient(new \GuzzleHttp\Client());
    $this->assertInstanceOf(\GuzzleHttp\Client::class, $this->config->getHttpClient());
  }

  public function testMiddleware(): void
  {
    $this->config->middleware()->use(new TestIgnoreMiddleware());
    $this->assertEquals(3, count($this->config->middleware()->getMiddlewares()));
  }

  public function testReset(): void
  {
    $this->config->setApiKey("123456");
    $this->config->setApiURL("https://example.com");
    if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
      $this->config->setRootDirectory(realpath("D:\\"));
    } else {
      $this->config->setRootDirectory(realpath("/tmp"));
    }
    $this->config->middleware()->use(new TestIgnoreMiddleware());

    $this->config->reset();

    $this->assertEquals("", $this->config->getApiKey());
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", $this->config->getApiURL());
    $this->assertEquals(rtrim(rtrim(__DIR__, '/tests'), '\tests') . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'composer', $this->config->getRootDirectory());
    $this->assertEquals(2, count($this->config->middleware()->getMiddlewares()));
  }
}

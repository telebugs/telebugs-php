<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Config;

class ConfigTest extends TestCase
{
  protected static $config;

  protected function setUp(): void
  {
    self::$config = Config::getInstance();
  }

  protected function tearDown(): void
  {
    self::$config->reset();
  }

  public function testSetApiKey()
  {
    self::$config->setApiKey("123456");
    $this->assertEquals("123456", self::$config->getApiKey());
  }

  public function testSetApiURL()
  {
    self::$config->setApiURL("https://api.telebugs.com/2024-03-28/errors");
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", self::$config->getApiURL());
  }

  public function testSetRootDirectory()
  {
    self::$config->setRootDirectory("/var/www/html");
    $this->assertEquals("/var/www/html", self::$config->getRootDirectory());
  }

  public function testConfigure()
  {
    self::$config->configure([
      'api_key' => "123456",
      'api_url' => "https://api.telebugs.com/2024-03-28/errors",
      'root_directory' => "/var/www/html"
    ]);

    $this->assertEquals("123456", self::$config->getApiKey());
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", self::$config->getApiURL());
    $this->assertEquals("/var/www/html", self::$config->getRootDirectory());
  }

  public function testSetHttpClient()
  {
    self::$config->setHttpClient(new \GuzzleHttp\Client());
    $this->assertInstanceOf(\GuzzleHttp\Client::class, self::$config->getHttpClient());
  }

  public function testReset()
  {
    self::$config->reset();

    $this->assertNull(self::$config->getApiKey());
    $this->assertEquals("https://api.telebugs.com/2024-03-28/errors", self::$config->getApiURL());
    $this->assertEquals("", self::$config->getRootDirectory());
  }
}

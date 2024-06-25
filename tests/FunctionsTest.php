<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Config;

use function Telebugs\configure;

class FunctionsTest extends TestCase
{
  public function testConfigure(): void
  {
    configure(['api_key' => "999"]);

    $this->assertEquals("999", Config::getInstance()->getApiKey());
  }
}

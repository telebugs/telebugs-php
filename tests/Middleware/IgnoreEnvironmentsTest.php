<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Middleware\IgnoreEnvironments;
use Telebugs\Report;

class IgnoreEnvironmentsTest extends TestCase
{
  public function testIgnoreEnvironmentsIgnoresWhenEnvMatches()
  {
    $report = new Report(new \Exception('test error'));
    $middleware = new IgnoreEnvironments('production', ['production']);
    $middleware($report);

    $this->assertTrue($report->ignored);
  }

  public function testIgnoreEnvironmentsDoesNotIgnoreWhenEnvDoesntMatch()
  {
    $report = new Report(new \Exception('test error'));
    $middleware = new IgnoreEnvironments('production', ['staging']);
    $middleware($report);

    $this->assertFalse($report->ignored);
  }

  public function testWeightMethod()
  {
    $middleware = new IgnoreEnvironments('', []);
    $this->assertEquals(-1000, $middleware->getWeight());
  }
}

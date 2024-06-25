<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Reporter;

class ReporterTest extends TestCase
{
    public function testReport()
    {
        $reporter = Reporter::getInstance();
        $this->assertInstanceOf(Reporter::class, $reporter);
    }
}

<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Reporter;

class ReporterTest extends TestCase {
    public function testReport() {
        $reporter = new Reporter();
        $this->expectOutputString("Reporting error: Error message\n");
        $reporter->report("Error message");
    }
}

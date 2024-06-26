<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Report;

class ReportTest extends TestCase
{
  public function testData(): void
  {
    $report = new Report(new \Exception("error message"));
    $report->data['errors'] = "error message";

    $this->assertEquals("error message", $report->data['errors']);
  }
}

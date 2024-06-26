<?php

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Report;

class ReportTest extends TestCase
{
  public function testDataWithNestedErrors(): void
  {
    try {
      throw new \Exception("error 1");
    } catch (\Exception $e1) {
      try {
        throw new \InvalidArgumentException("error 2", 0, $e1);
      } catch (\InvalidArgumentException $e2) {
        $r = new Report($e2);
      }
    }

    $error1 = $r->data['errors'][0];
    $error2 = $r->data['errors'][1];

    $this->assertEquals("InvalidArgumentException", $error1['type']);
    $this->assertEquals("error 2", $error1['message']);

    $this->assertEquals("Exception", $error2['type']);
    $this->assertEquals("error 1", $error2['message']);
  }

  public function testDataReporters(): void
  {
    $r = new Report(new \Exception());
    $this->assertEquals([Report::REPORTER], $r->data['reporters']);
  }
}

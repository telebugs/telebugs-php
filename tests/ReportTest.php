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

    $file1 = $error1['backtrace'][0]['file'];
    $line1 = $error1['backtrace'][0]['line'];
    $function1 = $error1['backtrace'][0]['function'];
    $this->assertMatchesRegularExpression('/TestCase.php$/', $file1);
    $this->assertEquals(1188, $line1);
    $this->assertEquals('testDataWithNestedErrors', $function1);

    $this->assertEquals("Exception", $error2['type']);
    $this->assertEquals("error 1", $error2['message']);

    $file2 = $error2['backtrace'][0]['file'];
    $line2 = $error2['backtrace'][0]['line'];
    $function2 = $error2['backtrace'][0]['function'];
    $this->assertMatchesRegularExpression('/TestCase.php$/', $file2);
    $this->assertEquals(1188, $line2);
    $this->assertEquals('testDataWithNestedErrors', $function2);
  }

  public function testDataReporters(): void
  {
    $r = new Report(new \Exception());
    $this->assertEquals([Report::REPORTER], $r->data['reporters']);
  }

  public function testIgnored(): void
  {
    $r = new Report(new \Exception());
    $this->assertFalse($r->ignored);

    $r->ignored = true;
    $this->assertTrue($r->ignored);
  }
}

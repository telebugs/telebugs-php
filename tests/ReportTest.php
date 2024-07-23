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
    $this->assertMatchesRegularExpression('/ReportTest.php$/', $error1['backtrace'][0]['file']);
    $this->assertEquals(17, $error1['backtrace'][0]['line']);
    $this->assertEquals('', $error1['backtrace'][0]['function']);
    $this->assertEquals('testDataWithNestedErrors', $error1['backtrace'][1]['function']);
    $this->assertMatchesRegularExpression('/ReportTest.php$/', $error1['backtrace'][0]['file']);
    $this->assertEquals([
      'start_line' => 15,
      'lines' => [
        '    } catch (\Exception $e1) {',
        '      try {',
        '        throw new \InvalidArgumentException("error 2", 0, $e1);',
        '      } catch (\InvalidArgumentException $e2) {',
        '        $r = new Report($e2);'
      ]
    ], $error1['backtrace'][0]['code']);

    $this->assertEquals("Exception", $error2['type']);
    $this->assertEquals("error 1", $error2['message']);
    $this->assertMatchesRegularExpression('/ReportTest.php$/', $error2['backtrace'][0]['file']);
    $this->assertEquals(14, $error2['backtrace'][0]['line']);
    $this->assertEquals('', $error2['backtrace'][0]['function']);
    $this->assertEquals('testDataWithNestedErrors', $error2['backtrace'][1]['function']);
    $this->assertMatchesRegularExpression('/ReportTest.php$/', $error2['backtrace'][0]['file']);
    $this->assertEquals([
      'start_line' => 12,
      'lines' => [
        '  {',
        '    try {',
        '      throw new \Exception("error 1");',
        '    } catch (\Exception $e1) {',
        '      try {'
      ]
    ], $error2['backtrace'][0]['code']);
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

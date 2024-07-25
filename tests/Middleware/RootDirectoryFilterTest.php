<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;

use Telebugs\Middleware\RootDirectoryFilter;
use Telebugs\Report;
use function Telebugs\configure;

class RootDirectoryFilterTest extends TestCase
{
  public function testRootDirectoryFilter()
  {
    if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
      $rootDirectory = realpath("D:\\");
    } else {
      $rootDirectory = realpath("/tmp");
    }

    configure(function ($config) use ($rootDirectory) {
      $config->setRootDirectory($rootDirectory);
    });

    $backtrace = [
      [
        'file' => $rootDirectory . '/app/models/user.rb',
        'line' => 1,
        'function' => 'save'
      ]
    ];

    // Create an exception with a custom stack trace
    $exception = new \Exception('test error');
    $reflection = new \ReflectionObject($exception);
    $traceProperty = $reflection->getProperty('trace');
    $traceProperty->setAccessible(true);
    $traceProperty->setValue($exception, $backtrace);

    $report = new Report($exception);

    $middleware = new RootDirectoryFilter($rootDirectory);
    $middleware($report);

    $frame = $report->data['errors'][0]['backtrace'][1];
    $this->assertEquals('app/models/user.rb', $frame['file']);
    $this->assertTrue($frame['root_dir']);
  }

  public function testMultipleBacktraceEntries()
  {
    if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
      $rootDirectory = realpath("D:\\");
    } else {
      $rootDirectory = realpath("/tmp");
    }
    $backtrace = [
      [
        'file' => $rootDirectory . '/app/controllers/users_controller.php',
        'line' => 10,
        'function' => 'index'
      ],
      [
        'file' => '/usr/local/lib/php/vendor/framework.php',
        'line' => 100,
        'function' => 'dispatch'
      ],
      [
        'file' => $rootDirectory . '/public/index.php',
        'line' => 5,
        'function' => 'run'
      ]
    ];

    $report = $this->createMock(Report::class);
    $report->data = ['errors' => [['backtrace' => $backtrace]]];

    $middleware = new RootDirectoryFilter($rootDirectory);
    $middleware($report);

    $processedBacktrace = $report->data['errors'][0]['backtrace'];
    $this->assertEquals('app/controllers/users_controller.php', $processedBacktrace[0]['file']);
    $this->assertTrue($processedBacktrace[0]['root_dir']);
    $this->assertEquals('/usr/local/lib/php/vendor/framework.php', $processedBacktrace[1]['file']);
    $this->assertFalse(isset($processedBacktrace[1]['root_dir']));
    $this->assertEquals('public/index.php', $processedBacktrace[2]['file']);
    $this->assertTrue($processedBacktrace[2]['root_dir']);
  }

  public function testEmptyBacktrace()
  {
    if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
      $rootDirectory = realpath("D:\\");
    } else {
      $rootDirectory = realpath("/tmp");
    }
    $report = $this->createMock(Report::class);
    $report->data = ['errors' => [['backtrace' => []]]];

    $middleware = new RootDirectoryFilter($rootDirectory);
    $middleware($report);

    $this->assertEmpty($report->data['errors'][0]['backtrace']);
  }

  public function testWeightMethod()
  {
    $middleware = new RootDirectoryFilter('/any/directory');
    $this->assertEquals(-999, $middleware->getWeight());
  }
}

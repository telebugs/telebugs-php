<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;
use Telebugs\Middleware\ComposerRootFilter;
use Telebugs\Report;

class ComposerRootFilterTest extends TestCase
{
  public function testComposerRootFilter()
  {
    $backtrace = [
      [
        'file' => getcwd() . '/tests/fixtures/vendor/telebugs/telebugs/src/foo.php',
        'line' => 10,
      ],
      [
        'file' => getcwd() . '/tests/fixtures/vendor/telebugs/telebugs/src/bar.php',
        'line' => 20,
      ],
    ];

    $report = $this->createMock(Report::class);
    $report->data = ['errors' => [['backtrace' => $backtrace]]];

    $middleware = new ComposerRootFilter();
    $middleware->setComposerDir(getcwd() . '/tests/fixtures/vendor');
    $middleware->readPackageVersions();
    $middleware($report);

    $expectedBacktrace = [
      ['file' => 'telebugs/telebugs (v1.2.3) src/foo.php', 'line' => 10],
      ['file' => 'telebugs/telebugs (v1.2.3) src/bar.php', 'line' => 20],
    ];

    $this->assertEquals($expectedBacktrace, $report->data['errors'][0]['backtrace']);
  }
}

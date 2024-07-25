<?php

declare(strict_types=1);

namespace Telebugs\Middleware;

use Telebugs\BaseMiddleware;

class RootDirectoryFilter extends BaseMiddleware
{
  private string $rootDirectory;

  public function __construct(string $rootDirectory)
  {
    $this->rootDirectory = $rootDirectory;
  }

  public function __invoke($report): void
  {
    foreach ($report->data['errors'] as &$error) {
      foreach ($error['backtrace'] as &$frame) {
        if (!isset($frame['file'])) {
          continue;
        }

        $file = $frame['file'];

        if (!str_starts_with($file, $this->rootDirectory)) {
          continue;
        }

        $frame['root_dir'] = true;
        $frame['file'] = preg_replace('/^' . preg_quote($this->rootDirectory, '/') . '\/?/', '', $file);
      }
    }
  }

  public function getWeight(): int
  {
    return -999;
  }
}

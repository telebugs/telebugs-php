<?php

declare(strict_types=1);

namespace Telebugs\Middleware;

use Telebugs\BaseMiddleware;

class ComposerRootFilter extends BaseMiddleware
{
  private ?string $composerDir = null;
  private array $packageVersions = [];
  private string $vendorPathPattern;

  public function __construct()
  {
    if (!class_exists('\Composer\Autoload\ClassLoader', false)) {
      return;
    }

    $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
    $fileName = $reflection->getFileName();
    if ($fileName === false) {
      return;
    }

    // Go up two levels from autoload.php
    $this->setComposerDir(dirname($fileName, 2));
  }

  public function setComposerDir(string $composerDir): void
  {
    $this->composerDir = $composerDir;
    $this->packageVersions = $this->readPackageVersions();
    $this->vendorPathPattern = '/^' . preg_quote($this->composerDir, '/') . '\/([^\/]+\/[^\/]+)\/(.+)$/';
  }

  public function __invoke($report): void
  {
    if (empty($this->packageVersions) || $this->composerDir === null) {
      return;
    }

    foreach ($report->data['errors'] as &$error) {
      $this->processBacktrace($error['backtrace']);
    }
  }

  public function getWeight(): int
  {
    return -999;
  }

  public function readPackageVersions(): array
  {
    if ($this->composerDir === null) {
      return [];
    }

    $installedJsonPath = $this->composerDir . '/composer/installed.json';

    if (!file_exists($installedJsonPath)) {
      return [];
    }

    $jsonContent = file_get_contents($installedJsonPath);
    if ($jsonContent === false) {
      return [];
    }
    $installedData = json_decode($jsonContent, true);
    if (!is_array($installedData)) {
      return [];
    }

    $packageVersions = [];

    // Check if the structure is Composer 2.x (with 'packages' key) or 1.x
    $packages = isset($installedData['packages']) && is_array($installedData['packages'])
      ? $installedData['packages']
      : $installedData;

    foreach ($packages as $package) {
      if (isset($package['name']) && isset($package['version'])) {
        $packageVersions[$package['name']] = $package['version'];
      }
    }

    return $packageVersions;
  }

  private function processBacktrace(array &$backtrace): void
  {
    foreach ($backtrace as &$frame) {
      if (!isset($frame['file'])) {
        continue;
      }

      $this->processFrame($frame);
    }
  }

  private function processFrame(array &$frame): void
  {
    if (!preg_match($this->vendorPathPattern, $frame['file'], $matches)) {
      return;
    }

    $packageName = $matches[1];
    $filePath = $matches[2];

    if (!isset($this->packageVersions[$packageName])) {
      return;
    }

    $version = $this->packageVersions[$packageName];

    $frame['file'] = $this->formatFile($packageName, $version, $filePath);
  }

  private function formatFile(string $packageName, string $version, string $filePath): string
  {
    return sprintf('%s (%s) %s', $packageName, $version, $filePath);
  }
}

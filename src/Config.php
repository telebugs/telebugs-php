<?php

namespace Telebugs;

use Telebugs\MiddlewareStack;
use Telebugs\Middleware\ComposerRootFilter;
use Telebugs\Middleware\RootDirectoryFilter;

class Config
{
  private const ERROR_API_URL = "https://api.telebugs.com/2024-03-28/errors";

  private \GuzzleHttp\Client $httpClient;

  private string $apiKey;
  private string $apiURL;
  private string $rootDirectory;
  public MiddlewareStack $middlewareStack;

  private static ?Config $instance = null;

  public static function getInstance(): Config
  {
    if (self::$instance === null) {
      self::$instance = new Config();
    }
    return self::$instance;
  }

  public function __construct()
  {
    $this->reset();
  }

  public function reset(): void
  {
    $this->httpClient = new \GuzzleHttp\Client();
    $this->apiKey = "";
    $this->apiURL = self::ERROR_API_URL;

    // Not sure if Composer is always available, better check first
    if (class_exists('\Composer\Autoload\ClassLoader', false)) {
      $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
      $fileName = $reflection->getFileName();
      if ($fileName === false) {
        $this->rootDirectory = $this->findRootDirectory();
      } else {
        $this->rootDirectory = dirname($fileName);
      }
    } else {
      $this->rootDirectory = $this->findRootDirectory();
    }

    $this->middlewareStack = new MiddlewareStack();
    $this->middlewareStack->use(new ComposerRootFilter());
    $this->middlewareStack->use(new RootDirectoryFilter($this->rootDirectory));
  }

  public function setHttpClient(\GuzzleHttp\Client $httpClient): void
  {
    $this->httpClient = $httpClient;
  }

  public function getHttpClient(): \GuzzleHttp\Client
  {
    return $this->httpClient;
  }

  public function getApiKey(): ?string
  {
    return $this->apiKey;
  }

  public function setApiKey(string $apiKey): void
  {
    $this->apiKey = $apiKey;
  }

  public function getApiURL(): string
  {
    return $this->apiURL;
  }

  public function setApiURL(string $apiURL): void
  {
    $this->apiURL = $apiURL;
  }

  public function getRootDirectory(): string
  {
    return $this->rootDirectory;
  }

  public function setRootDirectory(string $rootDirectory): void
  {
    realpath($rootDirectory) or throw new \Exception('Root directory does not exist: ' . $rootDirectory);

    $this->rootDirectory = realpath($rootDirectory);

    $this->middlewareStack->delete(RootDirectoryFilter::class);
    $this->middlewareStack->use(new RootDirectoryFilter($rootDirectory));
  }

  public function middleware(): MiddlewareStack
  {
    return $this->middlewareStack;
  }

  private function findRootDirectory(): string
  {
    // Start with the current directory
    $dir = __DIR__;

    // Go up the directory tree until we find a marker file/directory
    while ($dir !== '/' && $dir !== '\\') {
      // Check for common project root markers
      if (
        file_exists($dir . '/composer.json') ||
        file_exists($dir . '/vendor') ||
        file_exists($dir . '/.git')
      ) {
        return $dir;
      }
      $dir = dirname($dir);
    }

    // If we can't determine the root, return the current directory
    return __DIR__;
  }
}

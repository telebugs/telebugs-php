<?php

namespace Telebugs;

class Config
{
  private const ERROR_API_URL = "https://api.telebugs.com/2024-03-28/errors";

  private $apiKey;
  private $apiURL;
  private $rootDirectory;

  private static $instance;

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
    $this->apiKey = null;
    $this->apiURL = self::ERROR_API_URL;
    $this->rootDirectory = "";
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
    $this->rootDirectory = $rootDirectory;
  }

  public function configure(array $options): void
  {
    if (isset($options['api_key'])) {
      $this->setApiKey($options['api_key']);
    }
    if (isset($options['api_url'])) {
      $this->setApiURL($options['api_url']);
    }
    if (isset($options['root_directory'])) {
      $this->setRootDirectory($options['root_directory']);
    }
  }
}

<?php

namespace Telebugs;

class Config
{
  private const ERROR_API_URL = "https://api.telebugs.com/2024-03-28/errors";

  private \GuzzleHttp\Client $httpClient;

  private string $apiKey;
  private string $apiURL;
  private string $rootDirectory;

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
    $this->rootDirectory = "";
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
    $this->rootDirectory = $rootDirectory;
  }
}

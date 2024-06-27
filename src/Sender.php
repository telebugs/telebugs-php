<?php

namespace Telebugs;

use GuzzleHttp;

use Telebugs\Promise;
use Telebugs\Config;

use Psr\Http\Message\ResponseInterface;

class Sender
{
  private const CONTENT_TYPE = 'application/json';

  private const USER_AGENT = 'telebugs-php/' . Reporter::VERSION . ' (php/' . PHP_VERSION . ')';

  private Config $config;

  private string $authorization;

  public function __construct()
  {
    $this->config = Config::getInstance();
    $this->authorization = 'Bearer ' . $this->config->getApiKey();
  }

  public function send(string $data): Promise
  {
    $guzzlePromise = $this->config->getHttpClient()->postAsync($this->config->getApiURL(), [
      'body' => $data,
      'headers' => [
        'Content-Type' => self::CONTENT_TYPE,
        'User-Agent' => self::USER_AGENT,
        'Authorization' => $this->authorization
      ]
    ]);

    $promise = new Promise($guzzlePromise);
    return $promise->then(function (ResponseInterface $res) {
      return json_decode($res->getBody()->getContents(), true);
    });
  }
}

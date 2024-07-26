<?php

namespace Telebugs;

use Telebugs\Promise;
use Telebugs\Config;
use Telebugs\Report;

use Psr\Http\Message\ResponseInterface;

class Sender
{
  private const CONTENT_TYPE = 'application/json';

  private const USER_AGENT = 'telebugs-php/' . Reporter::VERSION . ' (php/' . PHP_VERSION . ')';

  private Config $config;

  public function __construct()
  {
    $this->config = Config::getInstance();
  }

  public function send(Report $report): Promise
  {
    $guzzlePromise = $this->config->getHttpClient()->postAsync($this->config->getApiURL(), [
      'body' => json_encode($report),
      'headers' => [
        'Content-Type' => self::CONTENT_TYPE,
        'User-Agent' => self::USER_AGENT,
        'Authorization' => 'Bearer ' . $this->config->getApiKey()
      ]
    ]);

    $promise = new Promise($guzzlePromise);
    return $promise->then(function (ResponseInterface $res) {
      return json_decode($res->getBody()->getContents(), true);
    });
  }
}

<?php

class Botley
{
  private const RESPONSES = [
    '/hello/i' => "Oh, hello there! Are you ready to have some fun?",
    '/how are you/i' => "I'm just a bunch of code, but thanks for asking! How about you?",
    '/what is your name/i' => "I'm Botley, your friendly (and sometimes cheeky) virtual assistant!",
    '/joke/i' => "Why don't scientists trust atoms? Because they make up everything!",
    '/bye|goodbye|exit/i' => "Goodbye! Don't miss me too much!",
    '/thank you|thanks/i' => "You're welcome! I'm here all week.",
    'default' => "I'm not sure what you mean, but it sounds intriguing!"
  ];

  public function __construct()
  {
    echo "Botley: Hello! I'm Botley, your virtual assistant. Type 'goodbye' to exit.\n";
  }

  public function start(): void
  {
    while (true) {
      echo "You: ";
      // @phpstan-ignore-next-line
      $userInput = trim(fgets(STDIN));
      $response = $this->respondTo($userInput);
      echo "Botley: $response\n";

      if (preg_match('/bye|goodbye|exit/i', $userInput)) {
        break;
      }
    }
  }

  private function respondTo(string $input): string
  {
    foreach (self::RESPONSES as $pattern => $response) {
      if ($pattern === 'default') {
        continue;
      }
      if (preg_match($pattern, $input)) {
        return $response;
      }
    }
    return self::RESPONSES['default'];
  }
}

// Start the conversation with Botley
$botley = new Botley();
$botley->start();

# Telebugs for PHP

[![Package Version](https://poser.pugx.org/telebugs/telebugs/v/stable)](https://packagist.org/packages/telebugs/telebugs)

Refreshingly simple error monitoring. Catch production errors automatically and
instantly report them to Telegram.

- [Official Documentation](https://telebugs.com/docs/integrations/php)
- [FAQ](https://telebugs.com/faq)
- [Telebugs News](https://t.me/TelebugsNews)
- [Telebugs Community](https://t.me/TelebugsCommunity)

## Introduction

Any PHP application or script can be integrated with
[Telebugs](https://telebugs.com) using the
[`telebugs/telebugs`](https://packagist.org/packages/telebugs/telebugs) package.
The package is designed to be simple and easy to use. It provides a simple API
to send errors to Telebugs, which will then be reported to your Telegram
project. This guide will help you get started with Telebugs for PHP.

For full details, please refer to the [Telebugs documentation](https://telebugs.com/docs/integrations/php).

## Installation

Install the package using Composer by executing:

```sh
composer require telebugs/telebugs
```

## Usage

This is the minimal example that you can use to test Telebugs for PHP with your
project:

```php
<?php
require 'vendor/autoload.php';

// Configure Telebugs as early as possible in your application.
Telebugs\configure(function ($config) {
    $config->setApiKey("YOUR_API_KEY");
});

try {
    1 / 0;
} catch (DivisionByZeroError $e) {
    Telebugs\report($e)->wait();
}

echo "An error was sent to Telebugs." .
    "It will appear in your dashboard shortly." .
    "A notification was also sent to your Telegram chat."
?>
```

Replace `YOUR_API_KEY` with your actual API key. You can ask
[@TelebugsBot](http://t.me/TelebugsBot) for your API key or find it in
your project's dashboard.

## Telebugs for PHP integrations

Telebugs for PHP is a standalone package that can be used with any PHP
application or script. It can be integrated with any PHP framework or library.

We provide official integrations for the following PHP platforms:

- [Laravel](https://github.com/telebugs/telebugs-laravel)

## PHP support policy

Telebugs for PHP supports the following PHP versions:

- PHP 8.1+

If you need support older PHP versions, please contact us at
[help@telebugs.com](mailto:help@telebugs.com).

## Development

After checking out the repo, run `composer install` to install dependencies.
Then, run `composer test` to run the tests.

To check the code with PHPStan, run `composer phpstan`.

To release a new version, simply push a new tag to the repository. Packagist
will automatically update the package.

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/telebugs/telebugs-php.

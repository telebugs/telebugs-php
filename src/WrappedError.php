<?php

declare(strict_types=1);

namespace Telebugs;

class WrappedError
{
  const MAX_NESTED_ERRORS = 3;

  private \Throwable $error;

  public function __construct(\Throwable $error)
  {
    $this->error = $error;
  }

  /**
   * @return array<\Throwable>
   */
  public function unwrap(): array
  {
    $errorList = [];
    $error = $this->error;

    while ($error && count($errorList) < self::MAX_NESTED_ERRORS) {
      $errorList[] = $error;
      $error = $error->getPrevious();
    }

    return $errorList;
  }
}

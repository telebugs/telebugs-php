<?php

namespace Telebugs;

class WrappedError
{
  const MAX_NESTED_ERRORS = 3;

  private $error;

  public function __construct($error)
  {
    $this->error = $error;
  }

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

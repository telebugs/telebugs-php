<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Telebugs\WrappedError;

class WrappedErrorTest extends TestCase
{
  public function testUnwrapsErrorsWithoutAPreviousError(): void
  {
    $error = new Exception();
    $wrapped = new WrappedError($error);
    $this->assertEquals([$error], $wrapped->unwrap());
  }

  public function testUnwrapsNoMoreThan3NestedErrors(): void
  {
    try {
      throw new Exception("error 1", 1);
    } catch (Exception $e1) {
      try {
        throw new Exception("error 2", 2, $e1);
      } catch (Exception $e2) {
        try {
          throw new Exception("error 3", 3, $e2);
        } catch (Exception $e3) {
          try {
            throw new Exception("error 4", 4, $e3);
          } catch (Exception $e4) {
            try {
              throw new Exception("error 5", 5, $e4);
            } catch (Exception $e5) {
              $wrapped = new WrappedError($e5);
              $unwrapped = $wrapped->unwrap();
              $this->assertCount(3, $unwrapped);
              $this->assertEquals([$e5, $e4, $e3], $unwrapped);
            }
          }
        }
      }
    }
  }
}

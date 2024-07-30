<?php

declare(strict_types=1);

namespace Telebugs\Tests;

use PHPUnit\Framework\TestCase;
use Telebugs\Truncator;

class TruncatorTest extends TestCase
{
  private const MAX_SIZE = 10;
  private const TRUNCATED = '[Truncated]';
  private const CIRCULAR = '[...]';

  private Truncator $truncator;

  protected function setUp(): void
  {
    $this->truncator = new Truncator(self::MAX_SIZE);
  }

  public function testTruncateShortString(): void
  {
    $input = 'Short';
    $result = $this->truncator->truncate($input);
    $this->assertSame($input, $result);
  }

  public function testTruncateLongString(): void
  {
    $input = 'This is a very long string that should be truncated';
    $expected = 'This is a ' . self::TRUNCATED;
    $result = $this->truncator->truncate($input);
    $this->assertSame($expected, $result);
  }

  public function testTruncateMultibyteString(): void
  {
    $input = 'こんにちは世界'; // "Hello World" in Japanese
    $expected = 'こんに' . self::TRUNCATED;
    $result = $this->truncator->truncate($input);
    $this->assertSame($expected, $result);
  }

  public function testTruncateArray(): void
  {
    $input = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k'];
    $result = $this->truncator->truncate($input);
    $this->assertCount(self::MAX_SIZE, $result);
    $this->assertSame(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'], $result);
  }

  public function testTruncateNestedArray(): void
  {
    $input = ['a' => ['b' => ['c' => ['d' => 'deep']]]];
    $result = $this->truncator->truncate($input);
    $this->assertSame(['a' => ['b' => ['c' => ['d' => 'deep']]]], $result);
  }

  public function testTruncateDeepNestedArray(): void
  {
    $input = ['a' => ['b' => ['c' => ['d' => ['e' => ['f' => ['g' => ['h' => ['i' => ['j' => ['k' => 'very deep']]]]]]]]]]];
    $result = $this->truncator->truncate($input);
    $this->assertSame(['a' => ['b' => ['c' => ['d' => ['e' => ['f' => ['g' => ['h' => ['i' => ['j' => self::TRUNCATED]]]]]]]]]], $result);
  }

  public function testTruncateAssociativeArray(): void
  {
    $input = [
      'key1' => 'value1',
      'key2' => 'value2',
      'key3' => 'value3',
      'key4' => 'This is a long value that should be truncated',
    ];
    $result = $this->truncator->truncate($input);
    $this->assertArrayHasKey('key4', $result);
    $this->assertSame('This is a ' . self::TRUNCATED, $result['key4']);
  }

  public function testTruncateStdClass(): void
  {
    $input = (object) [
      'prop1' => 'value1',
      'prop2' => 'value2',
      'prop3' => 'This is a long value that should be truncated',
    ];
    $result = $this->truncator->truncate($input);
    $this->assertObjectHasProperty('prop3', $result);
    $this->assertSame('This is a ' . self::TRUNCATED, $result->prop3);
  }

  public function testTruncateCircularReference(): void
  {
    $input = ['a' => 'value'];
    $input['b'] = &$input;
    $result = $this->truncator->truncate($input);
    $this->assertSame(['a' => 'value', 'b' => self::CIRCULAR], $result);
  }

  public function testTruncateCustomObject(): void
  {
    $input = new class
    {
      public function __toString()
      {
        return 'Custom object with a very long string representation';
      }
    };
    $result = $this->truncator->truncate($input);
    $this->assertSame('Custom obj' . self::TRUNCATED, $result);
  }

  public function testTruncateCustomObjectWithoutToString(): void
  {
    $input = new class
    {
    };
    $result = $this->truncator->truncate($input);
    $this->assertStringStartsWith('#<class@', $result);
    $this->assertStringEndsWith(self::TRUNCATED, $result);
  }

  public function testTruncateResource(): void
  {
    $input = fopen('php://memory', 'r');
    if ($input === false) {
      $this->fail('Failed to open resource');
    }
    $result = $this->truncator->truncate($input);
    $this->assertStringStartsWith('Resource id #', $result);
    fclose($input);
  }

  public function testTruncateClosure(): void
  {
    $input = function () {
      return 'This is a closure';
    };
    $result = $this->truncator->truncate($input);
    $this->assertSame('#<Closure>', $result);
  }

  public function testSelfReturningPrimitives(): void
  {
    $primitives = [1, 1.5, true, false, null];
    foreach ($primitives as $primitive) {
      $this->assertSame($primitive, $this->truncator->truncate($primitive));
    }
  }

  public function testReduceMaxSize(): void
  {
    $newMaxSize = $this->truncator->reduceMaxSize();
    $this->assertSame(5, $newMaxSize);

    $input = 'This should now be truncated earlier';
    $result = $this->truncator->truncate($input);
    $this->assertSame('This ' . self::TRUNCATED, $result);
  }

  public function testTruncateWithInvalidUTF8(): void
  {
    $input = "Valid UTF-8 \xC3\x28 Invalid UTF-8";
    $result = $this->truncator->truncate($input);
    $this->assertSame('Valid UTF-' . self::TRUNCATED, $result);
  }
}

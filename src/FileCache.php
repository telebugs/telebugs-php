<?php

declare(strict_types=1);

namespace Telebugs;

class FileCache
{
  public const MAX_SIZE = 50;

  // @phpstan-ignore missingType.iterableValue
  private static array $data = [];

  /**
   * Associates the value given by $value with the key given by $key.
   * Deletes entries that exceed MAX_SIZE.
   *
   * @param string $key
   * @param mixed $value
   */
  public static function set(string $key, $value): void
  {
    self::$data[$key] = $value;
    if (count(self::$data) > self::MAX_SIZE) {
      array_shift(self::$data);
    }
  }

  /**
   * Retrieve an object from the cache.
   */
  // @phpstan-ignore missingType.iterableValue
  public static function get(string $key): ?array
  {
    return self::$data[$key] ?? null;
  }

  /**
   * Checks whether the cache is empty. Needed only for the test suite.
   *
   * @return bool
   */
  public static function isEmpty(): bool
  {
    return empty(self::$data);
  }

  /**
   * Resets the cache.
   */
  public static function reset(): void
  {
    self::$data = [];
  }
}

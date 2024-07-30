<?php

declare(strict_types=1);

namespace Telebugs;

use JsonSerializable;

class Truncator
{
  private const CIRCULAR = '[...]';
  private const TRUNCATED = '[Truncated]';
  private const MAX_DEPTH = 10;

  private int $maxSize;

  public function __construct(int $maxSize)
  {
    $this->maxSize = $maxSize;
  }

  public function truncate(mixed $object): mixed
  {
    return $this->truncateRecursive($object, [], 0);
  }

  public function reduceMaxSize(): int
  {
    return $this->maxSize = intdiv($this->maxSize, 2);
  }

  private function truncateRecursive(mixed $object, array $seen, int $depth): mixed
  {
    if ($depth >= self::MAX_DEPTH) {
      return self::TRUNCATED;
    }

    if (is_object($object)) {
      $hash = spl_object_hash($object);
      if (isset($seen[$hash])) {
        return self::CIRCULAR;
      }
      $seen[$hash] = true;
    } elseif (is_array($object)) {
      foreach ($seen as $item) {
        if ($item === $object) {
          return self::CIRCULAR;
        }
      }
      $seen[] = $object;
    }

    switch (true) {
      case is_array($object):
        return $this->truncateArray($object, $seen, $depth);
      case $object instanceof \stdClass:
        return $this->truncateStdClass($object, $seen, $depth);
      case is_string($object):
        return $this->truncateString($object);
      case is_resource($object):
        return $this->truncateResource($object);
      case is_object($object):
        return $this->truncateObject($object);
      default:
        return $object;
    }
  }

  private function truncateString(string $str): string
  {
    $fixedStr = $this->replaceInvalidCharacters($str);
    if (strlen($fixedStr) <= $this->maxSize) {
      return $fixedStr;
    }

    $truncated = '';
    $byteCount = 0;
    $chars = preg_split('//u', $fixedStr, -1, PREG_SPLIT_NO_EMPTY);

    // @phpstan-ignore-next-line
    foreach ($chars as $char) {
      $charByteCount = strlen($char);
      if ($byteCount + $charByteCount > $this->maxSize) {
        break;
      }
      $truncated .= $char;
      $byteCount += $charByteCount;
    }

    return $truncated . self::TRUNCATED;
  }

  private function truncateObject(object|string $object): string
  {
    if ($object instanceof \Closure) {
      return '#<Closure>';
    }
    if ($object instanceof JsonSerializable || method_exists($object, '__toString')) {
      // @phpstan-ignore-next-line
      return $this->truncateString((string) $object);
    }
    return $this->truncateString(sprintf('#<%s>', get_class((object) $object)));
  }

  private function truncateArray(array $array, array $seen, int $depth): array
  {
    $truncatedArray = [];
    foreach ($array as $key => $value) {
      if (count($truncatedArray) >= $this->maxSize) {
        break;
      }
      $truncatedArray[$key] = $this->truncateRecursive($value, $seen, $depth + 1);
    }
    return $truncatedArray;
  }

  private function truncateStdClass(\stdClass $object, array $seen, int $depth): \stdClass
  {
    $truncatedObject = new \stdClass();
    // @phpstan-ignore-next-line
    foreach ($object as $key => $value) {
      if (count((array)$truncatedObject) >= $this->maxSize) {
        break;
      }
      $truncatedObject->$key = $this->truncateRecursive($value, $seen, $depth + 1);
    }
    return $truncatedObject;
  }

  // @phpstan-ignore-next-line
  private function truncateResource($resource): string
  {
    return "Resource id #" . intval($resource);
  }

  private function replaceInvalidCharacters(string $str): string
  {
    return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
  }
}

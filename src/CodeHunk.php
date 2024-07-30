<?php

declare(strict_types=1);

namespace Telebugs;

/**
 * Represents a small hunk of code consisting of a base line and a couple lines around it
 */
class CodeHunk
{
  private const MAX_LINE_LEN = 200;

  // How many lines should be read around the base line.
  private const AROUND_LINES = 2;

  // @phpstan-ignore missingType.iterableValue
  public static function get(?string $file, ?int $line): array
  {
    if ($file === null || $line === null) {
      return ['start_line' => 0, 'lines' => []];
    }

    $startLine = max($line - self::AROUND_LINES, 1);
    $lines = self::getLines($file, $startLine, $line + self::AROUND_LINES);

    if (empty($lines)) {
      return ['start_line' => 0, 'lines' => []];
    }

    return [
      'start_line' => $startLine,
      'lines' => $lines,
    ];
  }

  // @phpstan-ignore missingType.iterableValue
  private static function getLines(string $file, int $startLine, int $endLine): array
  {
    $lines = [];
    $cachedFile = self::getFromCache($file);

    if ($cachedFile === null) {
      return $lines;
    }

    $lineNumber = 1;
    foreach ($cachedFile as $line) {
      if ($lineNumber < $startLine) {
        $lineNumber++;
        continue;
      }

      if ($lineNumber > $endLine) {
        break;
      }

      $lines[] = rtrim(substr($line, 0, self::MAX_LINE_LEN));
      $lineNumber++;
    }

    return $lines;
  }

  // @phpstan-ignore missingType.iterableValue
  private static function getFromCache(string $file): array|null
  {
    $cachedFile = FileCache::get($file);

    if ($cachedFile !== null) {
      return $cachedFile;
    }

    try {
      $contents = file($file, FILE_IGNORE_NEW_LINES);
      if ($contents === false) {
        throw new \RuntimeException("Unable to read file: $file");
      }
      FileCache::set($file, $contents);
      return $contents;
    } catch (\Exception $e) {
      return null;
    }
  }
}

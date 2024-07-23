<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Telebugs\FileCache;

class FileCacheTest extends TestCase
{
  protected function tearDown(): void
  {
    FileCache::reset();
  }

  public function testSetWhenCacheLimitIsNotReached(): void
  {
    $maxSize = FileCache::MAX_SIZE;

    for ($i = 0; $i < $maxSize; $i++) {
      FileCache::set("key{$i}", ["value{$i}"]);
    }

    $this->assertEquals(["value0"], FileCache::get("key0"));
    $this->assertEquals(["value" . ($maxSize - 1)], FileCache::get("key" . ($maxSize - 1)));
  }

  public function testSetWhenCacheOverLimit(): void
  {
    $maxSize = 2 * FileCache::MAX_SIZE;

    for ($i = 0; $i < $maxSize; $i++) {
      FileCache::set("key{$i}", ["value{$i}"]);
    }

    $this->assertNull(FileCache::get("key49"));
    $this->assertEquals(["value50"], FileCache::get("key50"));
    $this->assertEquals(["value" . ($maxSize - 1)], FileCache::get("key" . ($maxSize - 1)));
  }
}

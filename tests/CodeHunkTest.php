<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Telebugs\CodeHunk;

class CodeHunkTest extends TestCase
{
  public function testGetWhenFileIsEmpty(): void
  {
    $hunk = CodeHunk::get(__DIR__ . "/fixtures/project_root/empty_file.php", 1);
    $this->assertEquals(['start_line' => 0, 'lines' => []], $hunk);
  }

  public function testGetWhenFileHasOneLine(): void
  {
    $hunk = CodeHunk::get(__DIR__ . "/fixtures/project_root/one_line.php", 1);
    $this->assertEquals(
      [
        'start_line' => 1,
        'lines' => ["Boom::new()->call();"]
      ],
      $hunk
    );
  }

  public function testGet(): void
  {
    $hunk = CodeHunk::get(__DIR__ . "/fixtures/project_root/code.php", 20);
    $this->assertEquals(
      [
        'start_line' => 18,
        'lines' => [
          "  }",
          "",
          "  public function start(): void",
          "  {",
          "    while (true) {"
        ]
      ],
      $hunk
    );
  }

  public function testGetWithEdgeCaseFirstLine(): void
  {
    $hunk = CodeHunk::get(__DIR__ . "/fixtures/project_root/code.php", 1);
    $this->assertEquals(
      [
        'start_line' => 1,
        'lines' => [
          "<?php",
          "",
          "class Botley"
        ]
      ],
      $hunk
    );
  }

  public function testGetWithEdgeCaseLastLine(): void
  {
    $hunk = CodeHunk::get(__DIR__ . "/fixtures/project_root/code.php", 50);
    $this->assertEquals(
      [
        'start_line' => 48,
        'lines' => [
          '',
          "// Start the conversation with Botley",
          "\$botley = new Botley();",
          "\$botley->start();"
        ]
      ],
      $hunk
    );
  }

  public function testGetWhenCodeLineIsTooLong(): void
  {
    $hunk = CodeHunk::get(__DIR__ . "/fixtures/project_root/long_line.php", 1);
    $this->assertEquals(
      [
        'start_line' => 1,
        'lines' => [
          "loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong"
        ]
      ],
      $hunk
    );
  }
}

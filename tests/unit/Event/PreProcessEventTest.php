<?php declare(strict_types = 1);

namespace DrupalCodeGenerator\Tests\Unit\Event;

use DrupalCodeGenerator\Asset\AssetCollection;
use DrupalCodeGenerator\Event\AssetPreProcess;
use PHPUnit\Framework\TestCase;

/**
 * A test for pre-process event.
 */
final class PreProcessEventTest extends TestCase {

  /**
   * Test callback.
   */
  public function testPreProcessEvent(): void {
    $assets = new AssetCollection();
    $event = new AssetPreProcess($assets, 'some/path', 'example', FALSE);
    self::assertSame($assets, $event->assets);
    self::assertSame('some/path', $event->destination);
    self::assertSame('example', $event->commandName);
    self::assertSame(FALSE, $event->isDry);
  }

}

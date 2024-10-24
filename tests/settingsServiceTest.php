<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PowerBoard\Services\SettingsService;


class SettingsServiceTest extends TestCase {
  public function test_getOptionName() {
    $settings = SettingsService::getInstance();
    $expected = 'test_option_name';

    $actual = $settings->getOptionName('test', ['option', 'name']);

    $this->assertSame($expected, $actual);
  }
}

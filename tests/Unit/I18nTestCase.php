<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\{ArrayCatalog, I18n};
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

abstract class I18nTestCase extends TestCase
{
    protected function setUp(): void
    {
        I18n::register(ArrayCatalog::new(
            (new Config)->set('translation.dir', __DIR__ . '/../Fixtures')
        ));
    }

    protected function tearDown(): void
    {
        I18n::flush();
    }
}

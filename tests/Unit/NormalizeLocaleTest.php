<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\I18n;
use Koded\I18n\I18nCatalog;
use Koded\I18n\NoCatalog;
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

class NormalizeLocaleTest extends TestCase
{
    public function test_should_normalize_locale()
    {
        $this->assertSame('en_US', I18n::catalog('en_US')->locale(),
            'The locale is normalized');
    }

    protected function setUp(): void
    {
        I18n::register(I18nCatalog::new((new Config)
            ->set('translation.catalog', NoCatalog::class)
            ->set('translation.locale', 'en_US_POSIX')
        ));
    }

    protected function tearDown(): void
    {
        I18n::flush();
    }
}

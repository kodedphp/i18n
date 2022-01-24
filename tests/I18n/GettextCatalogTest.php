<?php

namespace Tests\Koded\I18n\I18n;

use Koded\I18n\{ArrayCatalog, GettextCatalog, I18n, I18nCatalog};
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

class GettextCatalogTest extends TestCase
{
    public function test_supported_locales()
    {
        $this->assertSame(
            "A lazy fox's been jumped by a quick brown dog",
            __('quick-fox', ['fox', 'dog'], 'en_US')
        );

        $this->assertInstanceOf(
            ArrayCatalog::class,
            I18n::catalogs()['en_US']
        );

        $this->assertSame(
            'Schneller brauner Fuchs springt über den faulen Hund',
            __('quick-fox', ['Fuchs', 'Hund'])
        );
    }

    protected function setUp(): void
    {
        $config = (new Config)
            ->set('translation.catalog', GettextCatalog::class)
            ->set('translation.dir', __DIR__ . '/../Fixtures')
            ->set('translation.locale', 'de_DE');

        I18n::register(I18nCatalog::new($config));
    }

    protected function tearDown(): void
    {
        I18n::flush();
    }
}

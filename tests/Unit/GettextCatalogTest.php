<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\{ArrayCatalog, GettextCatalog, I18n, I18nCatalog, NoCatalog};
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

class GettextCatalogTest extends TestCase
{
    public function test_supported_locales()
    {
        $this->assertInstanceOf(
            GettextCatalog::class,
            I18n::catalogs()['de_DE']
        );

        $this->assertSame(
            'Schneller brauner Fuchs springt über den faulen Hund',
            __('quick-fox', ['Fuchs', 'Hund'])
        );

        $this->assertSame(
            'This string is passed through',
            __('This string is passed through', [])
        );
    }

    public function test_unsupported_locales()
    {
        $this->assertSame(
            "A lazy fox's been jumped by a quick brown dog",
            __('quick-fox', ['fox', 'dog'], 'en_US'),
            'Translated because en_US.php exist'
        );

        $this->assertInstanceOf(
            ArrayCatalog::class,
            I18n::catalogs()['en_US'],
            'Fallback to ArrayCatalog because en_US.php exist'
        );
    }

    public function test_fallback()
    {
        $this->assertSame(
            'quick-fox',
            __('quick-fox', ['fox', 'dog'], 'mk_MK'),
            'No mk_MK translation, the string is passed as-is'
        );

        $this->assertSame(
            'quick-fox',
            __('quick-fox', ['fox', 'dog'], 'it_IT'),
            'No it_IT translation, the string is passed as-is'
        );

        $this->assertSame(
            'This string is passed through',
            __('This string is passed through', [], 'mk_MK'),
            'No ml_MK translation, the string is passed as-is'
        );

        $this->assertSame(
            'This string is passed through',
            __('This string is passed through', [], 'fr_FR'),
            'No fr_FR translation, the string is passed as-is'
        );

        $this->assertInstanceOf(
            GettextCatalog::class,
            I18n::catalogs()['mk_MK'],
            'mk_MK locale is supported'
        );

        $this->assertInstanceOf(
            GettextCatalog::class,
            I18n::catalogs()['it_IT'],
            'it_IT locale is supported'
        );

        $this->assertInstanceOf(
            NoCatalog::class,
            I18n::catalogs()['fr_FR'],
            'fr_FR locale is NOT supported, no fr_FR.php, fallback to NoCatalog'
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

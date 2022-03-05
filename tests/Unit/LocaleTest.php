<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\{GettextCatalog, I18n, I18nCatalog};
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    public function test_locale()
    {
        $this->assertSame('mk_MK', I18n::locale(),
            'Default locale is set to mk_MK');
    }

    public function test_loaded_catalogs()
    {
        $this->assertArrayHasKey('mk_MK', I18n::catalogs());
        $this->assertArrayHasKey('de_DE', I18n::catalogs());
        $this->assertArrayNotHasKey('en_US', I18n::catalogs(),
            'en_US catalog is not loaded yet');
    }

    public function test_translation()
    {
        $this->assertSame('Здраво, anonymous', I18n::translate('hello.username', ['anonymous']));
        $this->assertSame('Здраво, anonymous', I18n::translate('hello.username', ['anonymous'], 'mk_MK'));
        $this->assertSame('Hi, anonymous', I18n::translate('hello.username', ['anonymous'], 'en_US'));
        $this->assertSame('Halo anonymous', I18n::translate('hello.username', ['anonymous'], 'de_DE'));

        $this->assertArrayHasKey('en_US', I18n::catalogs());

        $this->assertSame(
            'English',
            I18n::catalog('en_US')->language(),
            'Language name is loaded from .php because the .po file should not be supported');

        $this->assertEquals([
            'mk_MK' => 'Македонски',
            'de_DE' => 'Deutsch',
            'en_US' => 'English',
        ], I18n::languages());
    }

    public function test_language_name()
    {
        $this->assertSame(
            'Deutsch',
            I18n::catalog('de_DE')->language());

        $this->assertSame(
            'Македонски',
            I18n::catalog('mk_MK')->language());

        $this->assertSame(
            'it_IT',
            I18n::catalog('it_IT')->language(),
            'Language name defaults to locale because no translation exist');
    }

    public function test_url_locale_string()
    {
        $this->assertSame(
            'mk-mk',
            I18n::catalog('mk_MK')->urlized()
        );

        $this->assertSame(
            'de-de',
            I18n::catalog('de_DE')->urlized()
        );

        $this->assertSame(
            'it-it',
            I18n::catalog('it_IT')->urlized()
        );

        $this->assertSame(
            'en-us',
            I18n::catalog('en_US')->urlized()
        );

    }

    protected function setUp(): void
    {
        $config = (new Config)
            ->set('translation.locale', 'mk_MK')
            ->set('translation.dir', __DIR__ . '/../Fixtures');

        I18n::register(I18nCatalog::new($config), true);

        I18n::register(I18nCatalog::new($config
            ->set('translation.catalog', GettextCatalog::class)
            ->set('translation.locale', 'de_DE')
        ));
    }

    protected function tearDown(): void
    {
        I18n::flush();
    }
}

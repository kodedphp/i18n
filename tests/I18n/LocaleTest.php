<?php

namespace Tests\Koded\I18n\I18n;

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

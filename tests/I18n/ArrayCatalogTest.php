<?php

namespace Tests\Koded\I18n\I18n;

use Koded\I18n\{ArrayCatalog, DefaultFormatter, I18n};

class ArrayCatalogTest extends I18nTestCase
{
    public function test_default_catalog_object()
    {
        $catalog = I18n::catalogs()[I18n::DEFAULT_LOCALE];

        $this->assertArrayHasKey(
            I18n::DEFAULT_LOCALE,
            I18n::catalogs(),
            'The default locale is created');

        $this->assertInstanceOf(
            ArrayCatalog::class,
            $catalog,
            'The default catalog is an ArrayCatalog instance');

        $this->assertSame(
            I18n::DEFAULT_LOCALE,
            $catalog->locale(),
            'Default catalog locale is en_US');

        $this->assertInstanceOf(
            DefaultFormatter::class,
            $catalog->formatter(),
            'The default message formatter is a DefaultFormatter instance');

        $this->assertStringContainsString(
            '/Fixtures/',
            $catalog->directory(),
            'The default path for translation files is set in configuration'
        );
    }
}

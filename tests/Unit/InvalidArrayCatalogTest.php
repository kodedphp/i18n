<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\ArrayCatalog;
use Koded\I18n\I18n;
use Koded\I18n\I18nCatalog;
use Koded\I18n\NoCatalog;
use Koded\Stdlib\Config;

class InvalidArrayCatalogTest extends I18nTestCase
{
    public function test_invalid_array()
    {
        I18n::register(I18nCatalog::new((new Config)
            ->set('translation.locale', 'xx_XX')
            ->set('translation.dir', __DIR__ . '/../Fixtures')
            ->set('translation.catalog', ArrayCatalog::class)
        ));

        $this->assertInstanceOf(NoCatalog::class, I18n::catalogs()['xx_XX'],
            'Expected ArrayCatalog, but it fallback to NoCatalog (invalid array)');
    }
}

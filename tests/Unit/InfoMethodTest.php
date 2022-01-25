<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\I18n;

class InfoMethodTest extends I18nTestCase
{
    public function test_info_method()
    {
        $info = I18n::info();

        $this->assertArrayHasKey('locale', $info);
        $this->assertArrayHasKey('catalogs', $info);

        $this->assertSame(I18n::DEFAULT_LOCALE, $info['locale']);
        $this->assertArrayHasKey(I18n::DEFAULT_LOCALE, $info['catalogs']);
        $this->assertCount(1, $info['catalogs']);

        $catalog = $info['catalogs'][I18n::DEFAULT_LOCALE];
        $this->assertArrayHasKey('class', $catalog);
        $this->assertArrayHasKey('formatter', $catalog);
        $this->assertArrayHasKey('dir', $catalog);
        $this->assertArrayHasKey('locale', $catalog);
    }
}

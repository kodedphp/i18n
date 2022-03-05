<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\DefaultFormatter;
use Koded\I18n\I18n;
use Koded\I18n\NoCatalog;
use PHPUnit\Framework\TestCase;

class UsageWithoutInstantiationTest extends TestCase
{
    public function test()
    {
        $this->assertSame([], I18n::catalogs());
        $this->assertSame('en_US', I18n::locale());

        $catalog = I18n::catalog('en_US');
        $catalog2 = I18n::catalog('en_US');
        $this->assertSame($catalog, $catalog2,
            'Every catalog (by locale) is a singleton instance');

        $this->assertInstanceOf(NoCatalog::class, $catalog);
        $this->assertInstanceOf(DefaultFormatter::class, $catalog->formatter());

        $this->assertSame('en-us', $catalog->urlized());
        $this->assertSame('en_US', $catalog->locale());

        $this->assertSame('en_US', $catalog->language(),
            'Defaults to locale value, if language value is not available');

        $this->assertSame(
            'String is not translated, only formatted',
            $catalog->translate('messages', 'String is %s translated, %s', ['not', 'only formatted'])
        );
    }

    protected function tearDown(): void
    {
        I18n::flush();
    }
}

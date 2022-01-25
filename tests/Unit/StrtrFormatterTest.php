<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\{I18n, I18nCatalog, StrtrFormatter};
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

class StrtrFormatterTest extends TestCase
{
    private string $message = 'Quick brown :0 jumps over the lazy :1';

    public function test_instance()
    {
        $catalog = I18n::catalogs()[I18n::DEFAULT_LOCALE];
        $this->assertInstanceOf(StrtrFormatter::class, $catalog->formatter());
    }

    public function test_translation()
    {
        $this->assertSame(
            'Quick brown fox jumps over the lazy dog',
            I18n::translate($this->message, [':0' => 'fox', ':1' => 'dog']),
            'Catalog is in a static registry, expecting translation');

        $this->assertSame(
            'Quick brown fox jumps over the lazy dog',
            I18n::translate($this->message, [':0' => 'fox', ':1' => 'dog'], 'en_US'),
            'Catalog is in a static registry, expecting translation');

        $this->assertSame(
            $this->message,
            I18n::translate($this->message),
            'No replacement arguments, return as-is');
    }

    protected function setUp(): void
    {
        I18n::register(I18nCatalog::new(
            (new Config)->set('translation.formatter', StrtrFormatter::class)
        ));
    }

    protected function tearDown(): void
    {
        I18n::flush();
    }
}

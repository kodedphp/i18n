<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\{CurlyFormatter, I18n, I18nCatalog};
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;

class CurlyFormatterTest extends TestCase
{
    private string $message1 = 'Quick brown {0} jumps over the lazy {1}';
    private string $message2 = 'Quick brown {key1} jumps over the lazy {key2}';

    public function test_instance()
    {
        $catalog = I18n::catalogs()[I18n::DEFAULT_LOCALE];
        $this->assertInstanceOf(CurlyFormatter::class, $catalog->formatter());
    }

    public function test_translation_with_arguments_as_list()
    {
        $this->assertSame(
            'Quick brown fox jumps over the lazy dog',
            I18n::translate($this->message1, ['fox', 'dog'])
        );
    }

    public function test_translation_with_arguments_as_map()
    {
        $this->assertSame(
            'Quick brown fox jumps over the lazy dog',
            I18n::translate($this->message2, ['key1' => 'fox', 'key2' => 'dog'])
        );
    }

    public function test_translation_with_missing_arguments_as_map()
    {
        $this->assertSame(
            'Quick brown fox jumps over the lazy {key2}',
            I18n::translate($this->message2, ['key1' => 'fox'])
        );
    }

    public function test_translation_with_no_arguments()
    {
        $this->assertSame(
            'Quick brown {key1} jumps over the lazy {key2}',
            I18n::translate($this->message2)
        );
    }

    public function test_without_arguments()
    {
        $this->assertSame(
            $this->message1,
            I18n::translate($this->message1),
            'No replacement arguments, return as-is'
        );
    }

    protected function setUp(): void
    {
        I18n::register(I18nCatalog::new(
            (new Config)->set('translation.formatter', CurlyFormatter::class)
        ));
    }

    protected function tearDown(): void
    {
        I18n::flush();
    }
}

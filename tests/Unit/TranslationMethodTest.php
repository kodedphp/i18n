<?php

namespace Tests\Koded\I18n\Unit;

use Koded\I18n\{I18n, NoCatalog};

class TranslationMethodTest extends I18nTestCase
{
    private string $message = 'Quick brown %s jumps over the lazy %s';

    public function test_translation_method()
    {
        $this->assertSame(
            'Quick brown fox jumps over the lazy dog',
            I18n::translate($this->message, ['fox', 'dog']),
            'Catalog is in a static registry, expecting translation');

        $this->assertSame(
            'Quick brown fox jumps over the lazy dog',
            I18n::translate($this->message, ['fox', 'dog'], 'en_US'),
            'Catalog is in a static registry, expecting translation');

        $this->assertSame(
            $this->message,
            I18n::translate($this->message),
            'No replacement arguments, return as-is');
    }

    public function test_translation_without_loaded_catalog()
    {
        $this->assertSame(
            'Quick brown fox jumps over the lazy dog',
            I18n::translate($this->message, ['fox', 'dog'], 'de_DE'),
            'No de_DE catalog, translated by using only the formatter and NoCatalog');

        $this->assertInstanceOf(
            NoCatalog::class,
            I18n::catalogs()['de_DE'],
            'No de_DE catalog, defaults to NoCatalog instance'
        );
    }
}

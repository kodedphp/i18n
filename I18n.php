<?php declare(strict_types=1);

namespace Koded\I18n;

use Koded\Stdlib\Config;
use Throwable;
use function error_log;
use function ini_set;
use function strtr;
use function substr_count;
use function vsprintf;

interface I18nFormatter
{
    /**
     * Message formatter for argument replacement in the message.
     *
     * @param string $string
     * @param array $arguments
     * @return string The message with applied arguments (if any)
     */
    public function format(string $string, array $arguments): string;
}

final class StrtrFormatter implements I18nFormatter
{
    public function format(string $string, array $arguments): string
    {
        return $arguments ? strtr($string, $arguments) : $string;
    }
}

final class DefaultFormatter implements I18nFormatter
{
    public function format(string $string, array $arguments): string
    {
        return $arguments ? vsprintf($string, $arguments) : $string;
    }
}

class I18n
{
    public const DEFAULT_LOCALE = 'en_US';

    /*
     * Default configuration parameters for all catalogs.
     * Values are taken by the explicitly set default locale,
     * or the first loaded catalog instance.
     */
    private static ?string $catalog = null;
    private static ?string $formatter = null;
    private static ?string $directory = null;
    private static ?string $locale = null;

    /** @var array<string, I18nCatalog> */
    private static array $catalogs = [];

    // @codeCoverageIgnoreStart
    private function __construct() {}
    // @codeCoverageIgnoreEnd

    public static function translate(
        string $string,
        array  $arguments = [],
        string $locale = null): string
    {
        try {
            return static::$catalogs[$locale]->translate('messages', $string, $arguments);
        } catch (Throwable) {
            static::registerCatalog($locale ??= static::locale());
            return static::$catalogs[$locale]->translate('messages', $string, $arguments);
        }
    }

    public static function locale(): string
    {
        return static::$locale ??= static::normalizeLocale(\Locale::getDefault());
    }

    /**
     * @return array{string, I18nCatalog}
     */
    public static function catalogs(): array
    {
        return static::$catalogs;
    }

    /**
     * @return array{locale:string, catalogs:<string, array{class: string, formatter:string, dir:string, locale:string}>}
     */
    public static function info(): array
    {
        $catalogs = [];
        foreach (static::$catalogs as $locale => $instance) {
            $catalogs[$locale] = [
                'class' => $instance::class,
                'formatter' => $instance->formatter()::class,
                'dir' => $instance->directory(),
                'locale' => $instance->locale(),
            ];
        }
        return [
            'locale' => static::$locale,
            'catalogs' => $catalogs,
        ];
    }

    public static function register(
        I18nCatalog $catalog,
        bool        $asDefault = false): void
    {
        $locale = $catalog->locale();
        if ($asDefault || empty(static::$catalogs)) {
            static::setDefaultLocale($locale);
            static::$directory = $catalog->directory();
            static::$formatter = $catalog->formatter()::class;
            static::$catalog = $catalog::class;
        }
        static::$catalogs[$locale] = $catalog;
    }

    public static function flush(): void
    {
        static::$catalogs = [];
        static::$directory = null;
        static::$formatter = null;
        static::$catalog = null;
        static::$locale = null;
        ini_set('intl.default_locale', '');
        \Locale::setDefault('');
    }

    private static function registerCatalog(string $locale): void
    {
        if (isset(static::$catalogs[$locale])) {
            return;
        }
        static::$catalogs[$locale] = I18nCatalog::new((new Config)
            ->set('translation.locale', $locale)
            ->set('translation.dir', static::$directory)
            ->set('translation.formatter', static::$formatter)
            ->set('translation.catalog', static::$catalog)
        );
    }

    private static function setDefaultLocale(string $locale): void
    {
        static::$locale = $locale;
        ini_set('intl.default_locale', $locale);
        \Locale::setDefault($locale);
    }

    private static function normalizeLocale(string $locale): string
    {
        if (substr_count($locale, '_') > 1) {
            $locale = explode('_', $locale);
            $locale = "$locale[0]_$locale[1]";
        }
        return $locale;
    }
}

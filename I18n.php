<?php declare(strict_types=1);

namespace Koded\I18n;

use Koded\Stdlib\Config;
use Throwable;
use function ini_set;
use function strtr;
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
            return self::$catalogs[$locale]->translate('messages', $string, $arguments);
        } catch (Throwable) {
            self::registerCatalog($locale ??= self::locale());
            return self::$catalogs[$locale]->translate('messages', $string, $arguments);
        }
    }

    public static function locale(): string
    {
        return self::$locale ??= I18nCatalog::normalizeLocale(\Locale::getDefault());
    }

    /**
     * @return array{string, I18nCatalog}
     */
    public static function catalogs(): array
    {
        return self::$catalogs;
    }

    /**
     * @return array{locale:string, catalogs:<string, array{class: string, formatter:string, dir:string, locale:string}>}
     */
    public static function info(): array
    {
        $catalogs = [];
        foreach (self::$catalogs as $locale => $instance) {
            $catalogs[$locale] = [
                'class' => $instance::class,
                'formatter' => $instance->formatter()::class,
                'dir' => $instance->directory(),
                'locale' => $instance->locale(),
            ];
        }
        return [
            'locale' => self::$locale,
            'catalogs' => $catalogs,
        ];
    }

    public static function register(
        I18nCatalog $catalog,
        bool        $asDefault = false): void
    {
        $locale = $catalog->locale();
        if ($asDefault || empty(self::$catalogs)) {
            self::setDefaultLocale($locale);
            self::$directory = $catalog->directory();
            self::$formatter = $catalog->formatter()::class;
            self::$catalog = $catalog::class;
        }
        self::$catalogs[$locale] = $catalog;
    }

    public static function flush(): void
    {
        self::$catalogs = [];
        self::$directory = null;
        self::$formatter = null;
        self::$catalog = null;
        self::$locale = null;
        ini_set('intl.default_locale', '');
        \Locale::setDefault('');
    }

    private static function registerCatalog(string $locale): void
    {
        if (isset(self::$catalogs[$locale])) {
            return;
        }
        self::$catalogs[$locale] = I18nCatalog::new((new Config)
            ->set('translation.locale', $locale)
            ->set('translation.dir', self::$directory)
            ->set('translation.formatter', self::$formatter)
            ->set('translation.catalog', self::$catalog)
        );
    }

    private static function setDefaultLocale(string $locale): void
    {
        self::$locale = $locale;
        ini_set('intl.default_locale', $locale);
        \Locale::setDefault($locale);
    }
}

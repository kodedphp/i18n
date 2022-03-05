<?php

namespace Koded\I18n;

use Koded\Stdlib\Configuration;
use Throwable;
use function array_key_exists;
use function error_log;
use function getcwd;
use function rtrim;
use function str_replace;
use function strtolower;

abstract class I18nCatalog
{
    protected string $locale;
    protected string $directory;
    protected I18nFormatter $formatter;

    private function __construct(
        I18nFormatter $formatter,
        string        $directory,
        string        $locale)
    {
        $this->formatter = $formatter;
        $this->directory = rtrim($directory, '/');
        $this->locale = $this->initialize($locale);
    }

    public static function new(Configuration $conf): I18nCatalog
    {
        $catalog = $conf->get('translation.catalog', ArrayCatalog::class);
        $formatter = $conf->get('translation.formatter', DefaultFormatter::class);
        $instance = new $catalog(
            new $formatter,
            $directory = $conf->get('translation.dir', getcwd() . '/locale'),
            $locale = self::normalizeLocale($conf->get('translation.locale', I18n::DEFAULT_LOCALE))
        );
        if ($instance->supports($locale)) {
            return $instance;
        }
        if ($catalog !== ArrayCatalog::class) {
            error_log(" > ($locale) gettext not supported, trying ArrayCatalog ...");
            $conf->set('translation.catalog', ArrayCatalog::class);
            return static::new($conf);
        }
        // Last resort, passthru
        return new NoCatalog(new $formatter, $directory, $locale);
    }

    public static function normalizeLocale(string $locale): string
    {
        $locale = explode('_', str_replace('.', '_', $locale));
        return "$locale[0]_$locale[1]";
    }

    public function translate(
        string $domain,
        string $key,
        array  $arguments = [],
        int    $n = 0): string
    {
        return $this->formatter->format(
            $this->message($domain, $key, $n),
            $arguments
        );
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function formatter(): I18nFormatter
    {
        return $this->formatter;
    }

    public function urlized(): string
    {
        return str_replace('_', '-', strtolower($this->locale));
    }

    /**
     * Returns the localized display name for catalog's language.
     * Defaults to locale value if language value is not available.
     *
     * @return string
     */
    abstract public function language(): string;

    /**
     * Translates the message.
     *
     * @param string $domain
     * @param string $string
     * @param int $n
     * @return string
     */
    abstract protected function message(
        string $domain,
        string $string,
        int $n): string;

    /**
     * Checks if the locale is supported for this catalog,
     * or other specific requirements.
     *
     * @param string $locale
     * @return bool
     */
    abstract protected function supports(string $locale): bool;

    /**
     * Initialize the catalog object. This method is
     * called before supports().
     *
     * @param string $locale Desired locale to be initialized
     * @return string|false Returns the set locale,
     *                      or FALSE if initialization fails.
     */
    abstract protected function initialize(string $locale): string|false;
}

class NoCatalog extends I18nCatalog
{
    public function language(): string
    {
        return $this->locale ?: '';
    }

    protected function message(string $domain, string $string, int $n): string
    {
        return $string;
    }

    // @codeCoverageIgnoreStart
    protected function supports(string $locale): bool
    {
        return true;
    }
    // @codeCoverageIgnoreEnd

    protected function initialize(string $locale): string|false
    {
        return $locale;
    }
}

class ArrayCatalog extends I18nCatalog
{
    private array $data = [];

    public function language(): string
    {
        return $this->data['language'] ?? $this->locale;
    }

    protected function message(string $domain, string $string, int $n): string
    {
        return $this->data[$domain][$string] ?? $string;
    }

    protected function supports(string $locale): bool
    {
        return $this->locale === $locale;
    }

    protected function initialize(string $locale): string|false
    {
        try {
            $this->data = require($catalog = "$this->directory/$locale.php");
            if (false === array_key_exists('messages', $this->data)) {
                error_log("ERROR : i18n catalog $catalog is missing the messages array");
                return false;
            }
            return $locale;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}

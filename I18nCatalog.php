<?php

namespace Koded\I18n;

use Koded\Stdlib\Configuration;
use Throwable;
use function getcwd;
use function rtrim;
use function sprintf;

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
        $this->directory = rtrim($directory, '/') . '/';
        $this->locale = $this->initialize($locale);
    }

    public static function new(Configuration $conf): I18nCatalog
    {
        $catalog = $conf->get('translation.catalog', ArrayCatalog::class);
        $formatter = $conf->get('translation.formatter', DefaultFormatter::class);
        $instance = new $catalog(
            new $formatter,
            $directory = $conf->get('translation.dir', getcwd() . '/locales'),
            $locale = $conf->get('translation.locale', I18n::DEFAULT_LOCALE)
        );
        if ($instance->supports($locale)) {
            return $instance;
        }
        // Fallback to NoCatalog
        return new NoCatalog(new $formatter, $directory, $locale);
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

    /**
     * Translates the message.
     *
     * @param string $domain
     * @param string $string
     * @param int $n
     * @return string
     */
    abstract protected function message(string $domain, string $string, int $n): string;

    /**
     * Checks if the locale is supported by the catalog,
     * or other specific requirements for the catalog.
     *
     * @param string $locale
     * @return bool
     */
    abstract protected function supports(string $locale): bool;

    /**
     * Initialize the catalog object.
     *
     * @param string $locale Desired locale to be initialized
     * @return string|false Returns the set locale,
     *                      or FALSE if initialization fails.
     */
    abstract protected function initialize(string $locale): string|false;
}

class NoCatalog extends I18nCatalog
{
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

    protected function message(string $domain, string $string, int $n): string
    {
        return $this->data[$domain][$string] ?? $string;
    }

    protected function supports(string $locale): bool
    {
        return is_readable($this->directory . $locale . '.php');
    }

    protected function initialize(string $locale): string|false
    {
        try {
            $this->data = require($this->directory . $locale . '.php');
            return $locale;
        } catch (Throwable $e) {
            error_log(sprintf(
                    '[%s] Expects a catalog for %s. The error message was: %s',
                    __CLASS__, $locale, $e->getMessage())
            );
            return false;
        }
    }
}

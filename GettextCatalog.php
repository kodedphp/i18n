<?php

namespace Koded\I18n;

use function bind_textdomain_codeset;
use function bindtextdomain;
use function dgettext;
use function dngettext;
use function extension_loaded;
use function locale_get_display_language;
use function setlocale;
use function textdomain;

final class GettextCatalog extends I18nCatalog
{
    private array $domains = [];

    public function language(): string
    {
        return locale_get_display_language($this->locale, $this->locale);
    }

    protected function message(string $domain, string $string, int $n): string
    {
        $domain = $this->bindDomain($domain);
        return $n > 0
            ? dngettext($domain, $string, $string, $n) // TODO plural string
            : dgettext($domain, $string);
    }

    protected function supports(string $locale): bool
    {
        return $locale === $this->locale;
    }

    protected function initialize(string $locale): string|false
    {
        // @codeCoverageIgnoreStart
        if (false === extension_loaded('gettext')) {
            return false;
        }
        if (false === extension_loaded('intl')) {
            return false;
        }
        // @codeCoverageIgnoreEnd
        $this->bindDomain('messages');
        return setlocale(LC_MESSAGES, [$locale, "$locale.UTF-8", "$locale.utf8"])
            ? $locale
            : false;
    }

    private function bindDomain(string $domain): string
    {
        if (false === isset($this->domains[$domain])) {
            $this->directory = bindtextdomain($domain, $this->directory);
            $this->domains[$domain = textdomain($domain)] = true;
            bind_textdomain_codeset($domain, 'UTF-8');
        }
        return $domain;
    }
}

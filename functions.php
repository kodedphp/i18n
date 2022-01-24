<?php

use Koded\I18n\I18n;

function __(
    string $string,
    array $arguments = [],
    string $locale = null): string
{
    return I18n::translate($string, $arguments, $locale);
}

<?php

function __(
    string $string,
    array $arguments = [],
    string $locale = null): string
{
    return \Koded\I18n\I18n::translate($string, $arguments, $locale);
}

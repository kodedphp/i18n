<?php

use Koded\I18n\I18n;

function __(
    string $string,
    array  $arguments = [],
    string $locale = ''): string
{
    return I18n::catalog($locale)->translate('messages', $string, $arguments);
}

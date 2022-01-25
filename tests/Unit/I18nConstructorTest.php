<?php

namespace Tests\Koded\I18n\Unit;

use Error;
use Koded\I18n\I18n;

class I18nConstructorTest extends I18nTestCase
{
    public function test()
    {
        $this->expectException(Error::class);
        $this->expectErrorMessage('Call to private ');
        new I18n;
    }
}

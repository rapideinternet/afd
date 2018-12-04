<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class Date3Code extends DateCode
{
    protected static $code = Codes::DATE3;

    protected $description = 'Datum formaat EEJJ';

    protected $format = '!Y';

    protected $displayFormat = 'Y';
}

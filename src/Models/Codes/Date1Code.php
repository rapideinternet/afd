<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class Date1Code extends DateCode
{
    protected static $code = Codes::DATE1;

    protected $description = 'Datum formaat EEJJMMDD';

    protected $format = '!Ymd';
}

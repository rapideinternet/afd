<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class Date6Code extends DateCode
{
    protected static $code = Codes::DATE6;

    protected $description = 'Datum formaat EEJJMM';

    protected $format = '!Ym';
}

<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class Date5Code extends DateCode
{
    protected static $code = Codes::DATE5;

    protected $description = 'Datum formaat MMDD';

    protected $format = '!md';

    protected $displayFormat = 'd/m';
}

<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class MemoCode extends Code
{
    protected static $code = Codes::MEMO;

    protected $description = 'Memoveld, uitgebreid tekstveld; De lengte van een attribuut van dit type is maximaal 99999 tekens.';

    public function validateValue($value)
    {
        return strlen($value) < 99999;
    }
}

<?php

namespace SIVI\AFD\Models\Codes;

use DateTime;
use SIVI\AFD\Enums\Codes;

class TimeCode extends DateCode
{
    protected static $code = Codes::TIME;

    protected $description = 'Tijd formaat UUMM';

    protected $format = 'Hi';

    public function displayValue($value)
    {
        if ($value instanceof DateTime) {
            return $value->format('H:i');
        }

        $d = DateTime::createFromFormat($this->format, $value);
        return $d->format('H:i');
    }
}

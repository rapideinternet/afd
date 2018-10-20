<?php

namespace SIVI\AFD\Models\Codes;

use DateTime;

abstract class DateCode extends Code
{
    protected $format;

    protected function validateDateFormat($format, $value)
    {
        $d = DateTime::createFromFormat($format, $value);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $value;
    }

    public function validateValue($value)
    {
        $this->validateDateFormat($this->format, $value);
    }

    function format($value)
    {
        $d = DateTime::createFromFormat($this->format, $value);
        return $d->format($this->format);
    }
}

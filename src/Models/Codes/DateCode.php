<?php

namespace SIVI\AFD\Models\Codes;

use DateTime;

class DateCode extends Code
{
    protected $format;

    protected $displayFormat;

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

    public function formatValue($value)
    {
        $format = trim($this->format, '!');

        if ($value instanceof DateTime) {
            return $value->format($format);
        }

        $d = DateTime::createFromFormat($this->format, $value);
        return $d->format($format);
    }

    public function processValue($value)
    {
        return DateTime::createFromFormat($this->format, $value);
    }

    public function displayValue($value)
    {
        $format = $this->displayFormat;

        if ($value instanceof DateTime) {
            return $value->format($format);
        }

        $d = DateTime::createFromFormat($this->format, $value);
        return $d->format($format);
    }
}

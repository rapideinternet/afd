<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class BooleanCode extends Code
{
    protected static $code = Codes::BOOL;

    protected $description = 'Logische waarde J of N';

    public function validateValue($value)
    {
        return is_bool($value);
    }

    public function formatValue($value)
    {
        if ($value === true) {
            return 'J';
        }

        if ($value === false) {
            return 'N';
        }

        return '';
    }

    public function processValue($value)
    {
        if ($value === 'J') {
            return true;
        }

        if ($value === 'N') {
            return false;
        }

        return null;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function displayValue($value)
    {
        if ($value === true) {
            return 'Ja';
        }

        if ($value === false) {
            return 'Nee';
        }

        return '';
    }
}

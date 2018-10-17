<?php

namespace SIVI\ADN\Models\Formats;

use SIVI\ADN\Models\Interfaces\Validates;

class Format implements Validates
{
    public function validateValue($value)
    {
        return true;
    }
}

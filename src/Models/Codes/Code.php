<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Models\Interfaces\Validates;

abstract class Code implements Validates
{
    abstract function format($value);
}

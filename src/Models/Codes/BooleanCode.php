<?php

namespace SIVI\AFD\Models\Codes;

class BooleanCode extends Code
{
    public function validateValue($value)
    {
        return is_bool($value);
    }

    function format($value)
    {
        // TODO: Implement format() method.
    }
}

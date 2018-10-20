<?php

namespace SIVI\AFD\Models\Codes;

class MemoCode extends Code
{
    public function validateValue($value)
    {
        return strlen($value) < 99999;
    }

    function format($value)
    {
        // TODO: Implement format() method.
    }
}

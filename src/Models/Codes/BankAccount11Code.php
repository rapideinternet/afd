<?php

namespace SIVI\AFD\Models\Codes;

class BankAccount11Code extends Code
{
    public function validateValue($value)
    {
        return true; //TODO: use imap check library
    }

    function format($value)
    {
        // TODO: Implement format() method.
    }
}

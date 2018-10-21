<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class BankAccount11Code extends Code
{
    protected static $code = Codes::BANKACCOUNT11;

    protected $description = '11-proef voor bankrekeningnummer';


    public function validateValue($value)
    {
        return true; //TODO: use imap check library
    }

    function format($value)
    {
        // TODO: Implement format() method.
    }
}

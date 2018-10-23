<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class BankAccount97Code extends Code
{
    protected static $code = Codes::BANKACCOUNT97;

    protected $description = 'Bank Account Number 97-proef';

    public function validateValue($value)
    {
        return true; //TODO: use imap check library
    }
}

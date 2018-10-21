<?php


namespace SIVI\AFD\Models\Messages;


use SIVI\AFD\Enums\Messages;
use SIVI\AFD\Models\Message;

class ContractMessage extends Message
{
    protected $label = Messages::CONTRACT;

    protected static $type = Messages::CONTRACT;
}

<?php


namespace SIVI\AFD\Models\Messages;


use SIVI\AFD\Enums\Messages;
use SIVI\AFD\Models\Message;

class ContractMessage extends Message
{
    protected $label = Messages::CONTRACT;

    protected static $type = Messages::CONTRACT;

    public function __construct($label = null, array $entities = [], array $subMessages = [])
    {
        parent::__construct(Messages::CONTRACT, $entities, $subMessages);
    }
}

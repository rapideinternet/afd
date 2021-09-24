<?php


namespace SIVI\AFD\Models\Messages;


use SIVI\AFD\Enums\Messages;
use SIVI\AFD\Models\Message;

class ContractMessage extends Message
{
    protected $label = Messages::CONTRACT_MESSAGE;

    protected static $type = Messages::CONTRACT_MESSAGE;

    public function __construct($label = null, array $entities = [], array $subMessages = [])
    {
        parent::__construct(Messages::CONTRACT_MESSAGE, $entities, $subMessages);
    }
}

<?php


namespace SIVI\AFD\Models\Messages;


use SIVI\AFD\Enums\Messages;
use SIVI\AFD\Models\Message;

class BatchMessage extends Message
{
    protected $label = Messages::BATCH;

    protected static $type = Messages::BATCH;
}

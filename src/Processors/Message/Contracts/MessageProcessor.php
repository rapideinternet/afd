<?php

namespace SIVI\AFD\Processors\Message\Contracts;

use SIVI\AFD\Models\Message;

interface MessageProcessor
{
    public function process(Message $message): Message;
}

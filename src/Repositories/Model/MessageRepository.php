<?php

namespace SIVI\AFD\Repositories\Model;

use SIVI\AFD\Models\Codes\Code;
use SIVI\AFD\Models\Message;

class MessageRepository implements \SIVI\AFD\Repositories\Contracts\MessageRepository
{
    public function instantiateObject($label): Message
    {
        $class = Message::typeMap()[strtoupper($label)] ?? null;

        if ($class !== null) {
            return new $class($label);
        }

        return new Message($label);
    }

    /**
     * @param $code
     *
     * @return Code
     */
    public function getByLabel($label): Message
    {
        //TODO: override message format for different senders
        //TODO: implement a way to maintain your own overrides

        return $this->instantiateObject($label);
    }
}

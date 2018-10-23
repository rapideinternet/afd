<?php

namespace SIVI\AFD\Repositories\Contracts;


use SIVI\AFD\Models\Message;

interface MessageRepository
{
    /**
     * @param $label
     * @return Message
     */
    public function getByLabel($label): Message;
}

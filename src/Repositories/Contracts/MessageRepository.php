<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\Message;

interface MessageRepository
{
    /**
     * @param $label
     */
    public function getByLabel($label): Message;
}

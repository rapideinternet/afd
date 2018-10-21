<?php

namespace SIVI\AFD\Repositories\Contracts;


use SIVI\AFD\Models\Message;

interface MessageRepository
{
    public function getByLabel($label): Message;
}

<?php


namespace SIVI\AFD\Parsers\Contracts;


use SIVI\AFD\Models\Message;

interface Parser
{
    public function parse($content): Message;
}

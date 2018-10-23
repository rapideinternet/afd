<?php


namespace SIVI\AFD\Parsers\Contracts;


use SIVI\AFD\Models\Message;

interface XMLParser extends Parser
{
    public function parse($xmlString): Message;
}

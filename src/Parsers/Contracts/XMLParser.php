<?php


namespace SIVI\AFD\Parsers\Contracts;


use SIVI\AFD\Exceptions\InvalidParseException;
use SIVI\AFD\Models\Message;

interface XMLParser extends Parser
{
    /**
     * @throws InvalidParseException
     */
    public function parse(string $xmlString): Message;
}

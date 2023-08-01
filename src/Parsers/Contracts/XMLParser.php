<?php

namespace SIVI\AFD\Parsers\Contracts;

use SIVI\AFD\Exceptions\InvalidParseException;
use SIVI\AFD\Exceptions\NotImplementedException;
use SIVI\AFD\Models\Message;

interface XMLParser extends Parser
{
    /**
     * @throws InvalidParseException
     */
    public function parse(string $xmlString): Message;

    /**
     * @param callback(Message):void $callback
     *
     * @throws NotImplementedException
     */
    public function stream(string $content, callable $callback): void;
}

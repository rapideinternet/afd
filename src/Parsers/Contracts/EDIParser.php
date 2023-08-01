<?php

namespace SIVI\AFD\Parsers\Contracts;

use SIVI\AFD\Exceptions\InvalidParseException;
use SIVI\AFD\Models\Message;

interface EDIParser extends Parser
{
    public function parse(string $editContent): Message;

    /**
     * @param callback(Message):void $callback
     *
     * @throws InvalidParseException
     */
    public function stream(string $ediContent, callable $callback): void;
}

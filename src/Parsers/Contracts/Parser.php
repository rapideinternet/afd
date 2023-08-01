<?php

namespace SIVI\AFD\Parsers\Contracts;

use SIVI\AFD\Exceptions\InvalidParseException;
use SIVI\AFD\Models\Message;

interface Parser
{
    /**
     * @throws InvalidParseException
     */
    public function parse(string $content): Message;

    /**
     * @param callback(Message):void $callback
     *
     * @throws InvalidParseException
     */
    public function stream(string $content, callable $callback): void;
}

<?php


namespace SIVI\AFD\Parsers\Contracts;


use SIVI\AFD\Models\Message;

interface EDIParser extends Parser
{
    public function parse(string $editContent): Message;
}

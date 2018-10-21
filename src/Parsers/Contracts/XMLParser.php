<?php


namespace SIVI\AFD\Parsers\Contracts;


use SIVI\AFD\Models\Message;

interface XMLParser
{
    public function parse($xmlString) : Message;
}

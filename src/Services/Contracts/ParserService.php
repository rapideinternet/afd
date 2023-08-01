<?php

namespace SIVI\AFD\Services\Contracts;

use SIVI\AFD\Parsers\Contracts\Parser;

interface ParserService
{
    public function getParserByExtension($extension): Parser;
}

<?php

namespace SIVI\AFD\Processors\Content\Contracts;

use SIVI\AFD\Models\Message;

interface ContentProcessor
{
    public function process($extension, $content): Message;
}

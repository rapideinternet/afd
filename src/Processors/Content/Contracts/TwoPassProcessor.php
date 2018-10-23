<?php

namespace SIVI\AFD\Processors\Content\Contracts;

use SIVI\AFD\Models\Message;

interface TwoPassProcessor extends ContentProcessor
{
    public function process($extension, $content): Message;
}

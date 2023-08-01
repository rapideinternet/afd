<?php

namespace SIVI\AFD\Processors\Content\Contracts;

use SIVI\AFD\Models\Message;

interface TwoPassProcessor extends ContentProcessor
{
    public function process(string $extension, string $content): Message;

    /**
     * @param callable(Message):void $callback
     */
    public function stream(string $extension, string $content, callable $callback): void;
}

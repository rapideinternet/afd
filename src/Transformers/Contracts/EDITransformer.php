<?php

namespace SIVI\AFD\Transformers\Contracts;

use SIVI\AFD\Models\Message;

interface EDITransformer
{
    public function transform(Message $message): string;
}

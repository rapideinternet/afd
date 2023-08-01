<?php

namespace SIVI\AFD\Transformers\Contracts;

use SIVI\AFD\Models\Message;

interface XMLTransformer
{
    public function transform(Message $message): string;
}

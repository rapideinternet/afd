<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class AttachmentCode extends Code
{
    protected static $code = Codes::ATTACHMENT;

    protected $description = 'codeBase64 encoding, unbounded; De lengte van een attribuut van dit type is onbeperkt.';

    public function validateValue($value)
    {
        return (bool)base64_decode($value, true);
    }
}

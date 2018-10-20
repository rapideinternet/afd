<?php

namespace SIVI\AFD\Models\Codes;

class AttachmentCode extends Code
{
    public function validateValue($value)
    {
        return (bool)base64_decode($value, true);
    }

    function format($value)
    {
        // TODO: Implement format() method.
    }
}

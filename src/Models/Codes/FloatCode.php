<?php

namespace SIVI\AFD\Models\Codes;

class FloatCode extends Code
{
    protected $delimiter;

    protected $length;

    /**
     * CurrencyCode constructor.
     * @param $length
     * @param string $delimiter
     */
    protected function __construct($length, $delimiter = '.')
    {
        $this->length = $length;
        $this->delimiter = $delimiter;
    }

    public function validateValue($value)
    {
        if (($pos = strpos($value, $this->delimiter)) !== false) {
            return strlen(substr($value, $pos + 1)) <= $this->length;
        }

        return true;
    }

    function format($value)
    {
        // TODO: Implement format() method.
    }
}

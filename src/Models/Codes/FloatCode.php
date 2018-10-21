<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class FloatCode extends Code
{
    protected static $code = Codes::FLOAT;

    protected $description = 'Aantal met maximaal n decimalen';

    protected $delimiter;

    protected $length;

    public static $variableLength = true;

    /**
     * CurrencyCode constructor.
     * @param $length
     * @param string $delimiter
     */
    public function __construct($code, $delimiter = '.')
    {
        parent::__construct($code);

        $this->length = (int)substr($code, 1);
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

    public function process($value)
    {
        return (double)$value;
    }
}

<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class PercentageCode extends Code
{
    public static $variableLength = true;
    protected static $code        = Codes::PERCENTAGE;
    protected $description        = 'Percentage met maximaal n decimalen';
    protected $delimiter;
    protected $length;

    /**
     * CurrencyCode constructor.
     *
     * @param $length
     * @param string $delimiter
     */
    public function __construct($code, $delimiter = '.')
    {
        parent::__construct($code);

        $this->length    = (int)substr($code, 1);
        $this->delimiter = $delimiter;
    }

    public function validateValue($value)
    {
        if (($pos = strpos($value, $this->delimiter)) !== false) {
            return strlen(substr($value, $pos + 1)) <= $this->length;
        }

        return true;
    }

    public function processValue($value)
    {
        return (float)$value;
    }

    public function displayValue($value)
    {
        return number_format((float)$value, $this->length) . ' %';
    }
}

<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Enums\Codes;

class CurrencyCode extends Code
{
    public static $variableLength = true;
    protected static $code = Codes::CURRENCY;
    protected $description = 'Bedrag met maximaal n decimalen';
    protected $delimiter;
    protected $length;

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

    public function processValue($value)
    {
        return (double)$value;
    }

    public function formatValue($value)
    {
        return sprintf('&euro; %s', number_format((float)$value, $this->length), ',', '.');
    }
}

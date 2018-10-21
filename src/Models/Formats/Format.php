<?php

namespace SIVI\AFD\Models\Formats;

use SIVI\AFD\Exceptions\InvalidFormatException;
use SIVI\AFD\Models\Interfaces\Validates;

class Format implements Validates
{
    const ALPHA_NUMERIC = 'AN';
    const NUMERIC = 'N';

    const MAX_LENGTH_STRING = '..';

    /**
     * @var
     */
    protected $type;

    protected $maxLength = false;

    protected $value;

    protected $rawFormat;

    /**
     * Format constructor.
     * @param $format
     * @throws InvalidFormatException
     */
    public function __construct($format)
    {
        $this->type = $this->determineType($format);
        $this->maxLength = $this->determineMaxLength($format);
        $this->value = $this->determineValue($format);
    }

    /**
     * @param $format
     * @throws InvalidFormatException
     */
    protected function determineType($format)
    {
        if (substr(strtoupper($format), 0, 2) == self::ALPHA_NUMERIC) {
            return self::ALPHA_NUMERIC;
        }

        if (substr(strtoupper($format), 0, 1) == self::NUMERIC) {
            return self::NUMERIC;
        }

        throw new InvalidFormatException(sprintf('Could not find format %s', $format));
    }

    protected function determineMaxLength($format)
    {
        return substr($format, $this->type == self::NUMERIC ? 1 : 2, 2) == self::MAX_LENGTH_STRING;
    }

    protected function determineValue($format): int
    {
        $offset = ($this->type == self::NUMERIC ? 1 : 2) + ($this->maxLength ? 2 : 0);

        return (int)substr($format, $offset);
    }

    public function validateValue($value)
    {
        if ($this->type == self::ALPHA_NUMERIC) {
            if ($this->maxLength) {
                return ctype_alpha($value) && strlen($value) < $this->maxLength;
            }

            return ctype_alpha($value) && strlen($value) == $this->maxLength;
        }

        if ($this->type == self::NUMERIC) {
            if ($this->maxLength) {
                return is_numeric($value) && strlen($value) < $this->maxLength;
            }

            return is_numeric($value) && strlen($value) == $this->maxLength;
        }

        return false;
    }

    public function process($value)
    {
        return $this->type == self::NUMERIC ? (int)$value : $value;
    }
}

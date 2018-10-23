<?php

namespace SIVI\AFD\Models\Formats;

use SIVI\AFD\Exceptions\InvalidFormatException;
use SIVI\AFD\Models\Interfaces\ValueFormats;

class Format implements ValueFormats
{
    public const ALPHA_NUMERIC = 'AN';
    public const NUMERIC = 'N';

    public const MAX_LENGTH_STRING = '..';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $maxLength = false;

    /**
     * @var int
     */
    protected $value;

    /**
     * @var string
     */
    protected $rawFormat;

    /**
     * Format constructor.
     * @param $format
     * @throws InvalidFormatException
     */
    public function __construct($format)
    {
        $this->rawFormat = $format;
        $this->type = $this->determineType($format);
        $this->maxLength = $this->determineMaxLength($format);
        $this->value = $this->determineValue($format);
    }

    /**
     * @param $format
     * @return string
     * @throws InvalidFormatException
     */
    protected function determineType($format): string
    {
        if (substr(strtoupper($format), 0, 2) == self::ALPHA_NUMERIC) {
            return self::ALPHA_NUMERIC;
        }

        if (substr(strtoupper($format), 0, 1) == self::NUMERIC) {
            return self::NUMERIC;
        }

        throw new InvalidFormatException(sprintf('Could not find format %s', $format));
    }

    /**
     * @param $format
     * @return bool
     */
    protected function determineMaxLength($format): bool
    {
        return substr($format, $this->type == self::NUMERIC ? 1 : 2, 2) == self::MAX_LENGTH_STRING;
    }

    /**
     * @param $format
     * @return int
     */
    protected function determineValue($format): int
    {
        $offset = ($this->type == self::NUMERIC ? 1 : 2) + ($this->maxLength ? 2 : 0);

        return (int)substr($format, $offset);
    }

    /**
     * @param $value
     * @return bool
     */
    public function validateValue($value): bool
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

    /**
     * @param $value
     * @return int|mixed
     */
    public function processValue($value)
    {
        return $this->type == self::NUMERIC ? (int)$value : $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function formatValue($value)
    {
        // TODO: Implement formatValue() method.
    }
}

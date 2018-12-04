<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Exceptions\NotImplementedException;
use SIVI\AFD\Models\Interfaces\ValueFormats;

class Code implements ValueFormats
{
    /**
     * @var bool
     */
    public static $variableLength = false;
    public static $codeMap = [
        AttachmentCode::class,
        BankAccount11Code::class,
        BankAccount97Code::class,
        BooleanCode::class,
        CurrencyCode::class,
        Date1Code::class,
        Date3Code::class,
        Date5Code::class,
        Date6Code::class,
        FloatCode::class,
        MemoCode::class,
        PercentageCode::class,
        TimeCode::class
    ];
    /**
     * Codes Enum
     * @var string
     */
    protected static $code;
    /**
     * @var
     */
    protected $description;
    protected $rawCode;

    public function __construct($code)
    {
        $this->rawCode = $code;
    }

    /**
     * @return array
     */
    public static function codeMap()
    {
        $map = [];

        foreach (self::$codeMap as $class) {
            $map[$class::$code] = $class;
        }

        return $map;
    }

    /**
     * @param $value
     * @throws NotImplementedException
     */
    public function formatValue($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function displayValue($value)
    {
        return $value;
    }

    /**
     * @param $value
     * @return array|void
     * @throws NotImplementedException
     */
    public function validateValue($value)
    {
        throw new NotImplementedException('Invalid code configuration validateValue not implemented');
    }

    public function processValue($value)
    {
        return $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return false;
    }
}

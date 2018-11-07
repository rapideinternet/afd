<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Exceptions\NotImplementedException;
use SIVI\AFD\Models\Interfaces\ValueFormats;

class Code implements ValueFormats
{
    /**
     * Codes Enum
     * @var string
     */
    protected static $code;

    /**
     * @var
     */
    protected $description;

    /**
     * @var bool
     */
    public static $variableLength = false;

    protected $rawCode;

    /**
     * @param $value
     * @throws NotImplementedException
     */
    public function formatValue($value)
    {
        return $value;
    }

    public function __construct($code)
    {
        $this->rawCode = $code;
    }

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
}

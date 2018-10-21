<?php

namespace SIVI\AFD\Models\Codes;

use SIVI\AFD\Exceptions\NotImplementedException;
use SIVI\AFD\Models\Interfaces\Validates;

class Code implements Validates
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

    /**
     * @param $value
     * @throws NotImplementedException
     */
    public function format($value)
    {
        throw new NotImplementedException('Invalid code configuration format not implemented');
    }

    public function __construct($code)
    {

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

    public function process($value)
    {
        return $value;
    }
}

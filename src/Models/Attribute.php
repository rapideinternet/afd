<?php

namespace SIVI\AFD\Models;

use SIVI\AFD\Models\Codes\Code;
use SIVI\AFD\Models\CodesList\CodeList;
use SIVI\AFD\Models\Domain\Domain;
use SIVI\AFD\Models\Formats\Format;
use SIVI\AFD\Models\Interfaces\Validatable;
use SIVI\AFD\Models\Interfaces\Validates;

class Attribute implements Validatable
{
    /**
     * @var string
     */
    protected $label;

    protected $typeLabel;

    protected static $type;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $rawValue;

    /**
     * @var Domain
     */
    protected $domain;

    /**
     * @var Format
     */
    protected $format;

    /**
     * @var Code
     */
    protected $code;

    /**
     * @var CodeList
     */
    protected $codeList;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $explanation;

    protected static $typeMap = [];

    /**
     * Attribute constructor.
     * @param $value
     */
    public function __construct($label)
    {
        $this->setLabel($label);
    }

    /**
     * @return array
     */
    public static function typeMap()
    {
        $map = [];

        foreach (self::$typeMap as $class) {
            $map[$class::$type] = $class;
        }

        return $map;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $valid = [];

        if ($this->domain instanceof Validates) {
            $valid[] = $this->domain->validateValue($this->value);
        }

        if ($this->format instanceof Validates) {
            $valid[] = $this->format->validateValue($this->value);
        }

        if ($this->code instanceof Validates) {
            $valid[] = $this->code->validateValue($this->value);
        }

        if ($this->codeList instanceof Validates) {
            $valid[] = $this->codeList->validateValue($this->value);
        }

        return (bool)array_product($valid);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Attribute
     */
    public function setLabel(string $label): Attribute
    {
        $this->label = $label;
        $this->typeLabel = substr($label, 3);
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Attribute
     */
    public function setValue($value): Attribute
    {
        $this->rawValue = $value;

        if ($this->format) {
            $value = $this->format->process($value);
        }

        if ($this->code) {
            $value = $this->code->process($value);
        }

        if ($this->codeList) {
            $value = $this->codeList->process($value);
        }

        $this->value = $value;

        return $this;
    }

    /**
     * @return Domain
     */
    public function getDomain(): Domain
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     * @return Attribute
     */
    public function setDomain(Domain $domain): Attribute
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return Format
     */
    public function getFormat(): Format
    {
        return $this->format;
    }

    /**
     * @param Format $format
     * @return Attribute
     */
    public function setFormat(Format $format): Attribute
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return Code
     */
    public function getCode(): Code
    {
        return $this->code;
    }

    /**
     * @param Code $code
     * @return Attribute
     */
    public function setCode(Code $code): Attribute
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return CodeList
     */
    public function getCodeList(): CodeList
    {
        return $this->codeList;
    }

    /**
     * @param CodeList $codeList
     * @return Attribute
     */
    public function setCodeList(CodeList $codeList): Attribute
    {
        $this->codeList = $codeList;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Attribute
     */
    public function setDescription(string $description): Attribute
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getExplanation(): string
    {
        return $this->explanation;
    }

    /**
     * @param string $explanation
     * @return Attribute
     */
    public function setExplanation(string $explanation): Attribute
    {
        $this->explanation = $explanation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeLabel()
    {
        return $this->typeLabel;
    }

    /**
     * @return mixed
     */
    public function getCodeListDescription()
    {
        if ($this->codeList) {
            return $this->codeList->getValue($this->getValue());
        }
    }
}
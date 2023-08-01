<?php

namespace SIVI\AFD\Models;

use SIVI\AFD\Exceptions\AFDException;
use SIVI\AFD\Exceptions\NotImplementedException;
use SIVI\AFD\Models\CodeList\CodeList;
use SIVI\AFD\Models\Codes\Code;
use SIVI\AFD\Models\Domain\Domain;
use SIVI\AFD\Models\Formats\Format;
use SIVI\AFD\Models\Interfaces\Validatable;
use SIVI\AFD\Models\Interfaces\ValueFormats;

class Attribute implements Validatable, Interfaces\Attribute
{
    protected static string $type;

    protected static array $typeMap = [];

    protected string $label;

    protected string $typeLabel;

    protected $value;

    protected string $rawValue;

    protected Domain $domain;

    protected Format $format;

    protected Code $code;

    protected CodeList $codeList;

    protected string $description;

    protected ?string $explanation;

    /**
     * Attribute constructor.
     *
     * @throws AFDException
     */
    public function __construct(string $label)
    {
        $this->setLabel($label);
    }

    public static function typeMap(): array
    {
        $map = [];

        foreach (self::$typeMap as $class) {
            $map[$class::$type] = $class;
        }

        return $map;
    }

    /**
     * @throws AFDException
     */
    public static function formatTypeLabel(string $label): string
    {
        $formattedLabel = substr($label, 3);

        if ($formattedLabel === false) {
            throw new AFDException(sprintf('Could not format label "%s"', $label));
        }

        return $formattedLabel;
    }

    /**
     * @throws NotImplementedException
     */
    public function validate(): bool
    {
        $valid = [];

        if (isset($this->domain) && $this->domain instanceof ValueFormats) {
            $valid[] = $this->domain->validateValue($this->value);
        }

        if (isset($this->format) && $this->format instanceof ValueFormats) {
            $valid[] = $this->format->validateValue($this->value);
        }

        if (isset($this->code) && $this->code instanceof ValueFormats) {
            $valid[] = $this->code->validateValue($this->value);
        }

        if (isset($this->codeList) && $this->codeList instanceof ValueFormats) {
            $valid[] = $this->codeList->validateValue($this->value);
        }

        return count($valid) !== 0 && (bool)array_product($valid);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @throws AFDException
     */
    public function setLabel(string $label): Attribute
    {
        $this->label     = $label;
        $this->typeLabel = self::formatTypeLabel($label);

        return $this;
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    /**
     * @throws NotImplementedException
     */
    public function getFormattedValue()
    {
        $value = $this->value;

        if (isset($this->codeList)) {
            $value = $this->codeList->formatValue($value);
        }

        if (isset($this->code)) {
            $value = $this->code->formatValue($value);
        }

        if (isset($this->format)) {
            $value = $this->format->formatValue($value);
        }

        return $value;
    }

    public function getDisplayValue()
    {
        $value = $this->value;

        if (isset($this->codeList) && (is_string($value) || is_int($value))) {
            if (isset($this->format) && !$this->codeList->hasKey($value)) {
                $value = $this->format->formatValue($value, true);
            }

            return $this->codeList->displayValue($value);
        }

        if (isset($this->code)) {
            return $this->code->displayValue($value);
        }

        if (isset($this->format)) {
            return $this->format->displayValue($value);
        }

        return $value;
    }

    public function getDomain(): Domain
    {
        return $this->domain;
    }

    public function setDomain(Domain $domain): Attribute
    {
        $this->domain = $domain;

        return $this;
    }

    public function getFormat(): Format
    {
        return $this->format;
    }

    public function setFormat(Format $format): Attribute
    {
        $this->format = $format;

        return $this;
    }

    public function getCode(): Code
    {
        return $this->code;
    }

    public function setCode(Code $code): Attribute
    {
        $this->code = $code;

        return $this;
    }

    public function getCodeList(): CodeList
    {
        return $this->codeList;
    }

    public function setCodeList(CodeList $codeList): Attribute
    {
        $this->codeList = $codeList;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Attribute
    {
        $this->description = $description;

        return $this;
    }

    public function getExplanation(): string
    {
        return $this->explanation;
    }

    public function setExplanation(string $explanation): Attribute
    {
        $this->explanation = $explanation;

        return $this;
    }

    public function getTypeLabel(): string
    {
        return $this->typeLabel;
    }

    /**
     * @return mixed|null
     */
    public function getCodeListDescription()
    {
        if (isset($this->codeList) && ($value = $this->codeList->getValue($this->getValue())) !== null) {
            return $value;
        }

        return null;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): Attribute
    {
        $this->rawValue = (string)($value ?? '');

        if (isset($this->format)) {
            $value = $this->format->processValue($value);
        }

        if (isset($this->code)) {
            $value = $this->code->processValue($value);
        }

        if (isset($this->codeList)) {
            $value = $this->codeList->processValue($value);
        }

        $this->value = $value;

        return $this;
    }

    public function __clone()
    {
        if (isset($this->codeList)) {
            $this->codeList = clone $this->codeList;
        }

        if (isset($this->format)) {
            $this->format = clone $this->format;
        }
    }
}

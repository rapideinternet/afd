<?php

namespace SIVI\AFD\Models\CodeList;

use SIVI\AFD\Models\Interfaces\ValueFormats;

class CodeList implements ValueFormats
{
    /**
     * Name of the list.
     *
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */
    protected $external;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * CodeList constructor.
     *
     * @param $label
     */
    public function __construct($label)
    {
        $this->label = $label;
    }

    public function validateValue($value)
    {
        return isset($this->values[$value]);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): CodeList
    {
        $this->label = $label;

        return $this;
    }

    public function isExternal(): bool
    {
        return $this->external;
    }

    public function setExternal(bool $external): CodeList
    {
        $this->external = $external;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): CodeList
    {
        $this->description = $description;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): CodeList
    {
        $this->values = $values;

        return $this;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function getValue($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        if (is_string($key) || is_int($key)) {
            return collect($this->values)->mapWithKeys(function ($value, $key) {
                return [(int)$key => $value];
            })->get($key);
        }

        return null;
    }

    /**
     * @param $value
     *
     * @return int|mixed
     */
    public function processValue($value)
    {
        if (ctype_digit($value)) {
            return (int)$value;
        }

        return $value;
    }

    /**
     * @param $value
     */
    public function formatValue($value)
    {
        return $value;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasKey($key)
    {
        if (is_float($key)) {
            $key = (int)$key;
        }

        return array_key_exists($key, $this->values);
    }

    /**
     * @param $value
     */
    public function displayValue($value)
    {
        return $this->getValue($value);
    }
}

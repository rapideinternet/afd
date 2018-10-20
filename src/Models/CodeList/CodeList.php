<?php

namespace SIVI\AFD\Models\CodesList;

use SIVI\AFD\Models\Interfaces\Validates;

class CodeList implements Validates
{
    /**
     * Name of the list
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

    public function validateValue($value)
    {
        return isset($this->values[$value]);
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
     * @return CodeList
     */
    public function setLabel(string $label): CodeList
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExternal(): bool
    {
        return $this->external;
    }

    /**
     * @param bool $external
     * @return CodeList
     */
    public function setExternal(bool $external): CodeList
    {
        $this->external = $external;
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
     * @return CodeList
     */
    public function setDescription(string $description): CodeList
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @return CodeList
     */
    public function setValues(array $values): CodeList
    {
        $this->values = $values;
        return $this;
    }
}

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

    /**
     * @var string
     */
    protected $value;

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

    /**
     * Attribute constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->label = $value;
        $this->value = $value;
        $this->format = new Format();
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
}

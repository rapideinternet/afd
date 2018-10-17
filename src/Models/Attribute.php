<?php

namespace SIVI\ADN\Models;

use SIVI\ADN\Models\Codes\Code;
use SIVI\ADN\Models\CodesList\CodeList;
use SIVI\ADN\Models\Domain\Domain;
use SIVI\ADN\Models\Formats\Format;
use SIVI\ADN\Models\Interfaces\Validatable;
use SIVI\ADN\Models\Interfaces\Validates;

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
    public function validate()
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

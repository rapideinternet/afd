<?php

namespace SIVI\ADN\Models;

use SIVI\ADN\Enums\EntityTypes;
use SIVI\ADN\Enums\XSDAttributes;
use SIVI\ADN\Models\Interfaces\Validatable;

class Entity implements Validatable
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var Attribute[]
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $explanation;

    /**
     * Should be implemented in the lower classes
     *
     * @var array
     */
    protected $allowedAttributeTypes = [
        EntityTypes::CONTRACT_PAKKET => [
            XSDAttributes::MIN_OCURS => 0,
            XSDAttributes::MAX_OCURS => 1
        ],
        EntityTypes::CONTRACT_POLISONDERDEEL => [
            XSDAttributes::MIN_OCURS => 0,
            XSDAttributes::MAX_OCURS => 1
        ],
    ];

    public function __construct($label, array $attributes)
    {
        $this->attributes = $attributes;
        $this->label = $label;
    }

    public function validate()
    {
        $valid = [];

        foreach ($this->attributes as $attribute) {
            if ($attribute instanceof Validatable) {
                $valid[] = $attribute->validate();
            }
        }

        return (bool)array_product($valid);
    }
}

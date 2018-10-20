<?php

namespace SIVI\AFD\Models;

use SIVI\AFD\Enums\EntityTypes;
use SIVI\AFD\Enums\XSDAttributes;
use SIVI\AFD\Models\Interfaces\Validatable;

class Entity implements Validatable
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
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
            XSDAttributes::MAX_OCURS => 1,
        ],
        EntityTypes::CONTRACT_POLISONDERDEEL => [
            XSDAttributes::MIN_OCURS => 0,
            XSDAttributes::MAX_OCURS => 1,
        ],
    ];

    public function __construct($label, array $attributes)
    {
        $this->attributes = $attributes;
        $this->label = $label;
    }

    public function validate(): bool
    {
        $valid = [];

        foreach ($this->attributes as $attribute) {
            if ($attribute instanceof Validatable) {
                $valid[] = $attribute->validate();
            }
        }

        return (bool)array_product($valid);
    }

    /**
     * @param $label
     */
    public function hasAttribute($label): bool
    {

    }
}

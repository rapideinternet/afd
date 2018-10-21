<?php

namespace SIVI\AFD\Models;

use SIVI\AFD\Enums\AttributeTypes;
use SIVI\AFD\Models\Entities\ByEntity;
use SIVI\AFD\Models\Interfaces\Validatable;

class Entity implements Validatable
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected static $type;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $subEntities = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $explanation;

    protected static $typeMap = [
        ByEntity::class
    ];

    /**
     * Should be implemented in the lower classes
     *
     * @var array
     */
    protected $allowedAttributeTypes = [
    ];

    public function __construct($label, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->label = $label;
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

    public function getOrderNumber()
    {
        if ($this->hasAttribute(AttributeTypes::VOLGNUM)) {
            $attribute = array_first($this->attributes[AttributeTypes::VOLGNUM]);

            if ($attribute instanceof Attribute) {
                return $attribute->getValue();
            }
        }
    }

    /**
     * @param $label
     */
    public function hasAttribute($label): bool
    {
        return isset($this->attributes[$label]);
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
     * @return Entity
     */
    public function setLabel(string $label): Entity
    {
        $this->label = $label;
        $this->labelType = substr($label, 2);
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
     * @return Entity
     */
    public function setDescription(string $description): Entity
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
     * @return Entity
     */
    public function setExplanation(string $explanation): Entity
    {
        $this->explanation = $explanation;
        return $this;
    }

    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[$attribute->getTypeLabel()][] = $attribute;
    }

    public function addSubEntity(Entity $entity)
    {
        $orderNumber = $entity->getOrderNumber();

        if ($orderNumber === null) {
            $this->subEntities[$entity->getLabel()][] = $entity;
        } else {
            $this->subEntities[$entity->getLabel()][$orderNumber] = $entity;
        }
    }
}

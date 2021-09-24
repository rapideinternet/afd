<?php

namespace SIVI\AFD\Models;

use SIVI\AFD\Enums\AttributeTypes;
use SIVI\AFD\Models\Contracts\Entity as EntityContract;
use SIVI\AFD\Models\Entities\ByEntity;
use SIVI\AFD\Models\Interfaces\Validatable;

class Entity implements EntityContract, Validatable
{
    /**
     * @var string
     */
    protected static $type;
    /**
     * @var array
     */
    protected static $typeMap = [
        ByEntity::class
    ];
    /**
     * @var string
     */
    protected $label;
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
    /**
     * Should be implemented in the lower classes
     *
     * @var array
     */
    protected $allowedAttributeTypes = [
    ];

    /**
     * Entity constructor.
     * @param $label
     * @param array $attributes
     * @param array $subEntities
     */
    public function __construct(
        $label,
        array $attributes = [],
        array $subEntities = [],
        $description = null,
        $explanation = null
    ) {
        $this->setLabel($label);
        $this->attributes = $attributes;
        $this->subEntities = $subEntities;
        $this->description = $description;
        $this->explanation = $explanation;
    }

    /**
     * @return array
     */
    public static function typeMap()
    {
        $map = [];

        foreach (self::$typeMap as $class) {
            $map[strtoupper($class::$type)] = $class;
        }

        return $map;
    }

    public static function matchEntity(EntityContract $message): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $valid = [];

        foreach ($this->attributes as $attribute) {
            if ($attribute instanceof Validatable) {
                $valid[] = $attribute->validate();
            }
        }

        return !empty($valid) && array_product($valid);
    }

    /**
     * @param $label
     * @return array|Attribute[]
     */
    public function getAttributesByLabel($label)
    {
        return $this->attributes[$label] ?? [];
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

    /**
     * @param Attribute $attribute
     * @param int|null $orderNumber
     */
    public function addAttribute(Attribute $attribute, $orderNumber = null): void
    {
        if ($orderNumber !== null) {
            $this->attributes[$attribute->getTypeLabel()][$orderNumber] = $attribute;
        } else {
            $this->attributes[$attribute->getTypeLabel()][] = $attribute;
        }
    }

    /**
     * @param string $label
     */
    public function unsetAttributesByLabel(string $label): void
    {
//        unset($this->attributes[$label]);
    }

    /**
     * @param string $label
     */
    public function unsetSubEntityByLabel(string $label): void
    {
        unset($this->subEntities[$label]);
    }

    public function hasAttributeValue($label, $value)
    {
        if ($this->hasAttribute($label)) {
            /** @var Attribute $attribute */
            foreach ($this->attributes[$label] as $attribute) {
                return $attribute->getValue() == $value;
            }
        }

        return false;
    }

    /**
     * @param $label
     * @return bool
     */
    public function hasAttribute($label): bool
    {
        return isset($this->attributes[$label]) && count($this->attributes[$label]) > 0;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getSubEntities(): array
    {
        return $this->subEntities;
    }

    public function unsetSubEntityByLabelAndOrderNumber(string $entityType, int $orderNumber)
    {
        unset($this->subEntities[$entityType][$orderNumber]);
    }

    public function addSubEntities(array $subEntities)
    {
        foreach ($subEntities as $subEntity) {
            $this->addSubEntity($subEntity);
        }
    }

    /**
     * @param Entity $entity
     */
    public function addSubEntity(Entity $entity)
    {
        $orderNumber = $entity->getOrderNumber();

        if ($orderNumber === null) {
            $this->subEntities[$entity->getLabel()][] = $entity;
        } else {
            $this->subEntities[$entity->getLabel()][$orderNumber] = $entity;
        }
    }

    /**
     * @return null|string
     */
    public function getOrderNumber()
    {
        if ($this->hasAttribute(AttributeTypes::VOLGNUM)) {
            $attribute = array_first($this->attributes[AttributeTypes::VOLGNUM]);

            if ($attribute instanceof Attribute) {
                return $attribute->getValue();
            }
        }

        return null;
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
        return $this;
    }

    public function unsetAttributeByLabelAndOrderNumber(string $attributeType, int $orderNumber)
    {
        unset($this->subEntities[$attributeType][$orderNumber]);
    }
}

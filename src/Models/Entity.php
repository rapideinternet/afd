<?php

namespace SIVI\AFD\Models;

use SIVI\AFD\Enums\AttributeTypes;
use SIVI\AFD\Models\Contracts\Entity as EntityContract;
use SIVI\AFD\Models\Entities\ByEntity;
use SIVI\AFD\Models\Interfaces\Validatable;

class Entity implements EntityContract, Validatable
{
    protected static string $type;
    /**
     * @var array<class-string>
     */
    protected static array $typeMap = [
        ByEntity::class,
    ];

    protected string $label;
    /**
     * @var array<string, array<int|string, Attribute>>
     */
    protected array $attributes = [];
    /**
     * @var array<string, array<int|string, Entity>>
     */
    protected array $subEntities = [];

    protected ?string $description;

    protected ?string $explanation;
    /**
     * Should be implemented in the lower classes.
     *
     * @var array<class-string>
     */
    protected array $allowedAttributeTypes = [];

    /**
     * Entity constructor.
     *
     * @param array<string, array<int|string, Attribute>> $attributes
     * @param array<string, array<int|string, Entity>>    $subEntities
     */
    public function __construct(
        string $label,
        array $attributes = [],
        array $subEntities = [],
        ?string $description = null,
        ?string $explanation = null
    ) {
        $this->setLabel($label);
        $this->attributes  = $attributes;
        $this->subEntities = $subEntities;
        $this->description = $description;
        $this->explanation = $explanation;
    }

    public static function typeMap(): array
    {
        $map = [];

        foreach (self::$typeMap as $class) {
            $map[strtoupper($class::$type)] = $class;
        }

        return $map;
    }

    /**
     * TODO: implement or remove.
     */
    public static function matchEntity(EntityContract $message): bool
    {
        return false;
    }

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
     * @return array<int|string, Attribute>
     */
    public function getAttributesByLabel(string $label): array
    {
        return $this->attributes[$label] ?? [];
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Entity
    {
        $this->description = $description;

        return $this;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(?string $explanation): Entity
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
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

    public function unsetAttributesByLabel(string $label): void
    {
        unset($this->attributes[$label]);
    }

    public function unsetSubEntityByLabel(string $label): void
    {
        unset($this->subEntities[$label]);
    }

    public function hasAttributeValue(string $label, $value): bool
    {
        if ($this->hasAttribute($label)) {
            /** @var Attribute $attribute */
            foreach ($this->attributes[$label] as $attribute) {
                return $attribute->getValue() == $value;
            }
        }

        return false;
    }

    public function hasAttribute(string $label): bool
    {
        return isset($this->attributes[$label])
            && is_array($this->attributes[$label])
            && count($this->attributes[$label]) > 0;
    }

    /**
     * @return array<int|string, Attribute>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<int|string, Entity>
     */
    public function getSubEntities(): array
    {
        return $this->subEntities;
    }

    /**
     * @param int|string $orderNumber
     */
    public function unsetSubEntityByLabelAndOrderNumber(string $entityType, $orderNumber): void
    {
        unset($this->subEntities[$entityType][$orderNumber]);
    }

    /**
     * @param array<int|string, Entity> $subEntities
     */
    public function addSubEntities(array $subEntities): void
    {
        foreach ($subEntities as $subEntity) {
            $this->addSubEntity($subEntity);
        }
    }

    /**
     * @param int|string|null $orderNumber
     */
    public function addSubEntity(Entity $entity, $orderNumber = null): void
    {
        $orderNumber ??= $entity->getOrderNumber();

        if ($orderNumber === null) {
            $this->subEntities[$entity->getLabel()][] = $entity;
        } else {
            $this->subEntities[$entity->getLabel()][$orderNumber] = $entity;
        }
    }

    /**
     * @return string|null
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): Entity
    {
        $this->label = $label;

        return $this;
    }

    public function unsetAttributeByLabelAndOrderNumber(string $attributeType, int $orderNumber)
    {
        unset($this->subEntities[$attributeType][$orderNumber]);
    }

    public function __clone()
    {
        $this->attributes  = array_copy($this->attributes);
        $this->subEntities = array_copy($this->subEntities);
    }
}

<?php

namespace SIVI\AFD\Models;


use SIVI\AFD\Models\Interfaces\Validatable;

class Message implements Validatable
{
    /**
     * @var Entity
     */
    protected $entities;

    /**
     * Message constructor.
     * @param array $entities
     */
    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $valid = [];

        foreach ($this->entities as $entity) {
            if ($entity instanceof Validatable) {
                $valid[] = $entity->validate();
            }
        }

        return (bool)array_product($valid);
    }

    public function isPackage()
    {

    }
}

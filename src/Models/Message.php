<?php

namespace SIVI\ADN\Models;


use SIVI\ADN\Models\Interfaces\Validatable;

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
     * @return array|bool
     */
    public function validate()
    {
        $valid = [];

        foreach ($this->entities as $entity) {
            if ($entity instanceof Validatable) {
                $valid[] = $entity->validate();
            }
        }

        return (bool)array_product($valid);
    }
}

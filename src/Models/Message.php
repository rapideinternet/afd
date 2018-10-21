<?php

namespace SIVI\AFD\Models;


use SIVI\AFD\Models\Interfaces\Validatable;
use SIVI\AFD\Models\Messages\ContractMessage;

class Message implements Validatable
{
    protected $label;

    protected static $type;

    /**
     * @var Entity
     */
    protected $entities;

    protected $allowedEntities = [];

    protected $subMessages;

    protected $allowedSubMessages = [];

    protected static $typeMap = [
        ContractMessage::class
    ];

    /**
     * Message constructor.
     * @param null $label
     * @param array $entities
     * @param array $subMessages
     */
    public function __construct($label = null, array $entities = [], $subMessages = [])
    {
        $this->label = $label;
        $this->entities = $entities;
        $this->subMessages = $subMessages;
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

    public function addEntity(Entity $entity)
    {
        $orderNumber = $entity->getOrderNumber();

        if ($orderNumber === null) {
            $this->entities[$entity->getLabel()][] = $entity;
        } else {
            $this->entities[$entity->getLabel()][$orderNumber] = $entity;
        }
    }

    public function addSubmessage(Message $message)
    {
        $this->subMessages[$message->getLabel()][] = $message;
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }
}

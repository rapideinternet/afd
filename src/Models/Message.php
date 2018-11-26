<?php

namespace SIVI\AFD\Models;


use Carbon\Carbon;
use SIVI\AFD\Models\Contracts\Message as MessageContract;
use SIVI\AFD\Models\Interfaces\Validatable;
use SIVI\AFD\Models\Messages\BatchMessage;
use SIVI\AFD\Models\Messages\ContractMessage;

class Message implements MessageContract, Validatable
{
    /**
     * @var string
     */
    protected static $type;
    protected static $typeMap = [
        ContractMessage::class,
        BatchMessage::class
    ];
    /**
     * @var string
     */
    protected $label;
    /**
     * @var array
     */
    protected $entities = [];
    /**
     * @var array
     */
    protected $allowedEntities = [];
    /**
     * @var array
     */
    protected $subMessages = [];
    /**
     * @var string
     */
    protected $sender;
    /**
     * @var string
     */
    protected $receiver;
    /**
     * @var Carbon
     */
    protected $dateTime;
    /**
     * @var string
     */
    protected $messageId;
    /**
     * @var array
     */
    protected $allowedSubMessages = [];

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
            $map[strtoupper($class::$type)] = $class;
        }

        return $map;
    }

    public static function matchMessage(MessageContract $message): bool
    {
        //If a default message is matched this should return false at it wil
        //trigger an override
        return false;
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

        return !empty($valid) && array_product($valid);
    }

    public function isPackage()
    {

    }

    public function addEntity(Entity $entity, $orderNumber = null)
    {
        $orderNumber = $orderNumber ?? $entity->getOrderNumber();

        if ($orderNumber === null) {
            $this->entities[$entity->getLabel()][] = $entity;
        } else {
            $this->entities[$entity->getLabel()][$orderNumber] = $entity;
        }
    }

    public function addSubmessage(Message $message, $orderNumber = null)
    {
        if ($orderNumber === null) {
            $this->subMessages[$message->getLabel()][] = $message;
        } else {
            $this->subMessages[$message->getLabel()][$orderNumber] = $message;
        }
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @return array
     */
    public function getSubMessages(): array
    {
        return $this->subMessages;
    }

    /**
     * @return int
     */
    public function getSubMessagesCount(): int
    {
        $count = 0;

        foreach ($this->subMessages as $subMessages) {
            $count += count($subMessages);
        }

        return $count;
    }

    /**
     * @param $label
     * @param $entityLabel
     * @param null $value
     * @return bool
     */
    public function hasAttribute($label, $entityLabel, $value = null): bool
    {
        $result = [];

        if ($this->hasEntity($entityLabel)) /** @var Entity $entity */ {
            foreach ($this->entities[$entityLabel] as $entity) {
                $result[] = $entity->hasAttribute($label);
            }
        }

        return !empty($result) && array_product($result);
    }

    public function hasEntity($label): bool
    {
        return isset($this->entities[$label]) && count($this->entities[$label]) > 0;
    }

    /**
     * @param $label
     * @param $entityLabel
     * @param $value
     * @return bool
     */
    public function hasAttributeValue($label, $entityLabel, $value): bool
    {
        $result = [];

        if ($this->hasEntity($entityLabel)) /** @var Entity $entity */ {
            foreach ($this->entities[$entityLabel] as $entity) {
                $result[] = $entity->hasAttributeValue($label, $value);
            }
        }

        return !empty($result) && array_product($result);
    }

    /**
     * @return Carbon|null
     */
    public function getDateTime(): ?Carbon
    {
        return $this->dateTime;
    }

    /**
     * @param Carbon $dateTime
     */
    public function setDateTime(Carbon $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return string|null
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * @param string $messageId
     */
    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @return string|null
     */
    public function getSender(): ?string
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return string|null
     */
    public function getReceiver(): ?string
    {
        return $this->receiver;
    }

    /**
     * @param string $receiver
     */
    public function setReceiver(string $receiver): void
    {
        $this->receiver = $receiver;
    }
}

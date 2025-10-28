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
        BatchMessage::class,
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

    protected string $messageContentHash = '';

    /**
     * Message constructor.
     *
     * @param null  $label
     * @param array $subMessages
     */
    public function __construct($label = null, array $entities = [], $subMessages = [])
    {
        $this->label       = $label;
        $this->entities    = $entities;
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
        $orderNumber ??= $entity->getOrderNumber();

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

    public function getLabel()
    {
        return $this->label;
    }

    public function unsetEntitiesByLabel(string $label): void
    {
        unset($this->entities[$label]);
    }

    public function unsetSubmessagesByLabel(string $label)
    {
        unset($this->subMessages[$label]);
    }

    /**
     * @param $orderNumber
     */
    public function unsetSubmessagesByLabelAndOrderNumber(string $label, $orderNumber)
    {
        unset($this->subMessages[$label][$orderNumber]);
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function getSubMessages(): array
    {
        return $this->subMessages;
    }

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
     */
    public function hasAttribute($label, $entityLabel, $value = null): bool
    {
        $result = [];

        if ($this->hasEntity($entityLabel)) {
            /** @var Entity $entity */
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

    public function getDateTime(): ?Carbon
    {
        return $this->dateTime;
    }

    public function setDateTime(Carbon $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    public function getReceiver(): ?string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function unsetEntitiesByLabelAndOrderNumber(string $entityType, int $orderNumber)
    {
        unset($this->entities[$entityType][$orderNumber]);
    }

    public function __clone()
    {
        $this->entities    = array_copy($this->entities);
        $this->subMessages = array_copy($this->subMessages);
    }

    public function setMessageContentHash(string $messageHash): void
    {
        $this->messageContentHash = $messageHash;
    }

    public function getMessageContentHash(): string
    {
        return $this->messageContentHash;
    }
}

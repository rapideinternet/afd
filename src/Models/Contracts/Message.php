<?php


namespace SIVI\AFD\Models\Contracts;


use Carbon\Carbon;

interface Message
{

    /**
     * @return int
     */
    public function getSubMessagesCount(): int;

    /**
     * @param $label
     * @return bool
     */
    public function hasEntity($label): bool;

    /**
     * @param $label
     * @param $entityKey
     * @return bool
     */
    public function hasAttribute($label, $entityLabel): bool;

    /**
     * @param $label
     * @param $entityLabel
     * @param $value
     * @return bool
     */
    public function hasAttributeValue($label, $entityLabel, $value): bool;

    /**
     * @param Message $message
     * @return bool
     */
    public static function matchMessage(Message $message): bool;

    /**
     * @return Carbon|null
     */
    public function getDateTime(): ?Carbon;

    /**
     * @param Carbon $dateTime
     */
    public function setDateTime(Carbon $dateTime): void;

    /**
     * @return string|null
     */
    public function getMessageId(): ?string;

    /**
     * @param string $messageId
     */
    public function setMessageId(string $messageId): void;

    /**
     * @return string|null
     */
    public function getSender(): ?string;

    /**
     * @param string $sender
     */
    public function setSender(string $sender): void;

    /**
     * @return string|null
     */
    public function getReceiver(): ?string;

    /**
     * @param string $receiver
     */
    public function setReceiver(string $receiver): void;
}

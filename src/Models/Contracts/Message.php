<?php

namespace SIVI\AFD\Models\Contracts;

use Carbon\Carbon;

interface Message
{
    public function getSubMessagesCount(): int;

    /**
     * @param $label
     */
    public function hasEntity($label): bool;

    /**
     * @param $label
     * @param $entityKey
     */
    public function hasAttribute($label, $entityLabel): bool;

    /**
     * @param $label
     * @param $entityLabel
     * @param $value
     */
    public function hasAttributeValue($label, $entityLabel, $value): bool;

    public static function matchMessage(Message $message): bool;

    public function getDateTime(): ?Carbon;

    public function setDateTime(Carbon $dateTime): void;

    public function getMessageId(): ?string;

    public function setMessageId(string $messageId): void;

    public function getSender(): ?string;

    public function setSender(string $sender): void;

    public function getReceiver(): ?string;

    public function setReceiver(string $receiver): void;
}

<?php


namespace SIVI\AFD\Models\Contracts;


interface Message
{
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
}

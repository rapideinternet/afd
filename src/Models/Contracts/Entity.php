<?php


namespace SIVI\AFD\Models\Contracts;


interface Entity
{
    /**
     * @param $label
     * @return mixed
     */
    public function hasAttribute($label);

    /**
     * @param $label
     * @param $value
     * @return mixed
     */
    public function hasAttributeValue($label, $value);

    /**
     * @param Entity $message
     * @return bool
     */
    public static function matchEntity(Entity $message): bool;
}

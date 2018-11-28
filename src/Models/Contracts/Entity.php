<?php

namespace SIVI\AFD\Models\Contracts;

use SIVI\AFD\Models\Interfaces\Attribute;

interface Entity
{
    /**
     * @param $label
     * @return mixed
     */
    public function hasAttribute($label);

    /**
     * @param $label
     * @return array|Attribute[]
     */
    public function getAttributesByLabel($label);

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

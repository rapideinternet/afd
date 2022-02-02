<?php

namespace SIVI\AFD\Models\Contracts;

use SIVI\AFD\Models\Interfaces\Attribute;

interface Entity
{
    
    public function hasAttribute(string $label): bool;

    /**
     * @return array<string|int, Attribute>
     */
    public function getAttributesByLabel(string $label): array;

    /**
     * @param mixed $value
     */
    public function hasAttributeValue(string $label, $value): bool;

    public static function matchEntity(Entity $message): bool;
}

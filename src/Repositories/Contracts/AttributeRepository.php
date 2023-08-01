<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;

interface AttributeRepository
{
    /**
     * @param $label
     */
    public function instantiateObject($label): Attribute;

    /**
     * @param $label
     * @param null $value
     */
    public function getByLabel($label, $value = null): ?Attribute;

    public function getByEntity(Entity $entity);
}

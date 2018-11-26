<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;

interface AttributeRepository
{
    /**
     * @param $label
     * @return Attribute
     */
    public function instantiateObject($label): Attribute;

    /**
     * @param $label
     * @param null $value
     * @return Attribute|null
     */
    public function getByLabel($label, $value = null): ?Attribute;

    /**
     * @param Entity $entity
     * @return mixed
     */
    public function getByEntity(Entity $entity);
}

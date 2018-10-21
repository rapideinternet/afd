<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\Attribute;
use SIVI\AFD\Models\Entity;

interface AttributeRepository
{
    public function getByLabel($label, $value = null): Attribute;

    public function getByEntity(Entity $entity);
}

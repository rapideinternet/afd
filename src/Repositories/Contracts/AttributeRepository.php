<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\Entity;

interface AttributeRepository
{
    public function getByLabel($label);

    public function getByEntity(Entity $entity);
}

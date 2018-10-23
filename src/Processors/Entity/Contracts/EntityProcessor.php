<?php

namespace SIVI\AFD\Processors\Entity\Contracts;

use SIVI\AFD\Models\Entity;

interface EntityProcessor
{
    public function process(Entity $entity): Entity;
}

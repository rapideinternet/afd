<?php

namespace SIVI\AFD\Repositories\Contracts;


use SIVI\AFD\Models\Entity;

interface EntityRepository
{
    public function getByLabel($label): Entity;
}

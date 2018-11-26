<?php

namespace SIVI\AFD\Repositories\Contracts;


use SIVI\AFD\Models\Entity;

interface EntityRepository
{
    /**
     * @param $label
     * @return Entity
     */
    public function instantiateObject($label): Entity;


    /**
     * @param $label
     * @return Entity|null
     */
    public function getByLabel($label): ?Entity;
}

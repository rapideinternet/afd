<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\Entity;

interface EntityRepository
{
    /**
     * @param $label
     */
    public function instantiateObject($label): Entity;

    /**
     * @param $label
     */
    public function getByLabel($label): ?Entity;
}

<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\CodeList\CodeList;

interface CodeListRepository
{
    /**
     * @param $label
     */
    public function instantiateObject($label): CodeList;

    /**
     * @param $label
     */
    public function findByLabel($label): ?CodeList;
}

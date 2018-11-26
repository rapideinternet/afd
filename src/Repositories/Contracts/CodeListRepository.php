<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\CodeList\CodeList;

interface CodeListRepository
{
    /**
     * @param $label
     * @return CodeList
     */
    public function instantiateObject($label): CodeList;

    /**
     * @param $label
     * @return CodeList|null
     */
    public function findByLabel($label): ?CodeList;
}

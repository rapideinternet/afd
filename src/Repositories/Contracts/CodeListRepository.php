<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\CodesList\CodeList;

interface CodeListRepository
{
    /**
     * @param $label
     * @return CodeList
     */
    public function findByLabel($label): CodeList;
}

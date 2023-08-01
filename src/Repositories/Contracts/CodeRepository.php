<?php

namespace SIVI\AFD\Repositories\Contracts;

use SIVI\AFD\Models\Codes\Code;

interface CodeRepository
{
    /**
     * @param $code
     */
    public function instantiateObject($code): Code;

    /**
     * @param $code
     *
     * @return Code
     */
    public function findByCode($code): ?Code;
}

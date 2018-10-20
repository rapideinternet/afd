<?php

namespace SIVI\AFD\Models\Interfaces;

interface Validates
{
    /**
     * @param $value
     * @return array
     */
    public function validateValue($value);
}

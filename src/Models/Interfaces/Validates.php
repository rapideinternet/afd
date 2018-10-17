<?php

namespace SIVI\ADN\Models\Interfaces;

interface Validates
{
    /**
     * @param $value
     * @return array
     */
    public function validateValue($value);
}

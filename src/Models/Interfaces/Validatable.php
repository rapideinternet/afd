<?php

namespace SIVI\AFD\Models\Interfaces;

interface Validatable
{
    /**
     * @return bool
     */
    public function validate(): bool;
}

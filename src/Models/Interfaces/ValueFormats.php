<?php

namespace SIVI\AFD\Models\Interfaces;

interface ValueFormats
{
    /**
     * @param $value
     * @return bool
     */
    public function validateValue($value);

    /**
     * @param $value
     * @return mixed
     */
    public function processValue($value);

    /**
     * @param $value
     * @return mixed
     */
    public function formatValue($value);
}

<?php

namespace SIVI\AFD\Models\Interfaces;

interface ValueFormats
{
    /**
     * @param $value
     *
     * @return bool
     */
    public function validateValue($value);

    /**
     * @param $value
     */
    public function processValue($value);

    /**
     * @param $value
     */
    public function formatValue($value);

    /**
     * @param $value
     */
    public function displayValue($value);

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasKey($key);
}

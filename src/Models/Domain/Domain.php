<?php

namespace SIVI\AFD\Models\Domain;

use SIVI\AFD\Models\Interfaces\ValueFormats;

class Domain implements ValueFormats
{
    public function validateValue($value)
    {
        // TODO: Implement validateValue() method.
    }

    /**
     * @param $value
     * @return mixed
     */
    public function processValue($value)
    {
        // TODO: Implement processValue() method.
    }

    /**
     * @param $value
     * @return mixed
     */
    public function formatValue($value)
    {
        // TODO: Implement formatValue() method.
    }

    /**
     * @param $value
     * @return mixed
     */
    public function displayValue($value)
    {
        // TODO: Implement displayValue() method.
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        // TODO: Implement hasKey() method.
    }
}

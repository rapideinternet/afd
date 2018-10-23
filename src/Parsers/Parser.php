<?php


namespace SIVI\AFD\Parsers;


use SIVI\AFD\Models\Message;

abstract class Parser
{
    abstract public function parse($xmlString): Message;

    /**
     * @param $value
     * @return array|mixed|null
     */
    protected function processValue($value)
    {
        $value = (array)$value;

        if (count($value) == 1) {
            return array_first($value);
        }

        if (count($value) > 1) {
            return $value;
        }

        return null;
    }
}

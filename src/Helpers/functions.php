<?php

if (!function_exists('array_copy')) {
    function array_copy(array $array): array
    {
        $copy = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $copy[$key] = array_copy($value);
            } elseif (is_object($value)) {
                $copy[$key] = clone $value;
            } else {
                $copy[$key] = $value;
            }
        }

        return $copy;
    }
}
<?php
declare(strict_types=1);

namespace core\helpers;

class ArrayHelper
{
    /**
     * @param $array
     * @param $key
     * @param $default
     *
     * @return mixed|null
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_object($array) && property_exists($array, $key)) {
            return $array->$key;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if ($key && ($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        if (is_object($array)) {
            return $array->$key;
        }

        return $default;
    }
}
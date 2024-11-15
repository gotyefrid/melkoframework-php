<?php
declare(strict_types=1);

namespace core\helpers;

use Closure;

class ArrayHelper
{
    /**
     * Implemented from Yii2
     * @param array|object $array
     * @param array|string|callable $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof Closure) {
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

        if (!is_object($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }

        if ($key && ($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (!is_object($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (is_object($array) && property_exists($array, $key)) {
            return $array->$key;
        }

        return $default;
    }
}
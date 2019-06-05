<?php

namespace mxhei\helpers;

class Arr
{
    /**
     * @param $array
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }
    
        $keys = explode('.', $key);
    
        while (count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    
        return $array;
    }
    
    /**
     * @param      $array
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
    
        if (isset($array[$key])) {
            return $array[$key];
        }
    
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        
        return $array;
    }
    
    /**
     * @param array $array
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public static function add($array, $key, $value)
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }
        
        return $array;
    }
    
    /**
     * @param array $array
     *
     * @return array
     */
    public static function divide($array)
    {
        return [
            array_keys($array),
            array_values($array),
        ];
    }
    
    /**
     * get first item or Return the first element in an array passing a given truth test.
     * @param      $array
     * @param null $callback
     * @param null $default
     *
     * @return mixed|null
     */
    public static function first($array, $callback = null, $default = null)
    {
        $item = reset($array);
        if (!$callback && is_callable($callback)) {
            foreach($array as $key=>$value) {
                if (call_user_func_array($callback, [$key, $value])) {
                    return $value;
                }
            }
            return $default;
        }
        
        return $item;
    }
    
    /**
     * get last item or Return the last element in an array passing a given truth test
     * @param      $array
     * @param null $callback
     * @param null $default
     *
     * @return mixed|null
     */
    public static function last($array, $callback = null, $default = null)
    {
        return static::first(array_reverse($array), $callback, $default);
    }
    
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param array $array
     *
     * @return array
     */
    public static function flatten($array)
    {
        $return = [];
        array_walk_recursive(
            $array,
            function ($x) use (&$return) {
                $return[] = $x;
            }
        );
        
        return $return;
    }
    
    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array        $array
     * @param array|string $keys
     */
    public static function remove(&$array, $keys)
    {
        $original = &$array;
        
        foreach ((array) $keys as $key) {
            $parts = explode('.', $key);
            while (count($parts) > 1) {
                $part = array_shift($parts);
                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                }
            }
            unset($array[array_shift($parts)]);
            // clean up after each pass
            $array = &$original;
        }
    }
    
    /**
     * Get a value from the array, and remove it.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);
        static::remove($array, $key);
        
        return $value;
    }
    
    /**
     * Pluck an array of values from an array.
     *
     * @param array  $array
     * @param string $value
     * @param string $key
     *
     * @return array
     */
    public static function pluck($array, $value, $key = null)
    {
        $results = [];
        
        foreach ($array as $item) {
            $itemValue = is_object($item) ? $item->{$value} : $item[$value];
            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = is_object($item) ? $item->{$key} : $item[$key];
                $results[$itemKey] = $itemValue;
            }
        }
        
        return $results;
    }
    
    /**
     * Arr::merge($arr1, $arr2, .., $arrN);
     *
     * @param array $arr1
     * @param array $arr2
     *
     * @return array|mixed
     */
    public static function merge($arr1, $arr2)
    {
        $args = func_get_args();
        
        return call_user_func_array('array_merge', $args);
    }
    
    /**
     * array merge recursive
     *
     * @param $arr1
     * @param $arr2
     *
     * @return array|mixed
     */
    public static function mergeRecursive($arr1, $arr2)
    {
        $args = func_get_args();
        $res = array_shift($args);
        
        while (!empty($args)) {
            foreach(array_shift($args) as $k=>$v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = static::mergeRecursive($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
    
        return $res;
    }
}
<?php

namespace mxhei\helpers;

class Event
{
    /**
     * @var array $events
     * @example
     * [
     *  'before.request' => [
     *          '0' => [
     *              function(&$params){},
     *              'Test::request(&$params)',
     *              [new Test(), 'run']
     *          ],
     *      ]
     * ]
     */
    protected static $events = [];
    
    /**
     * 绑定事件
     *
     * @param     $name
     * @param     $callback
     * @param int|bool $weight 0,1,2 true
     * @param bool $overlay
     */
    public static function on($name, $callback, $weight = 0, $overlay = false)
    {
        isset(self::$events[$name]) || self::$events[$name] = [];
        if (is_array($callback) && !is_callable($callback)) {
            if (!array_key_exists('_overlay', $callback) || !$callback['_overlay']) {
                $overlay = true;
            }
            unset($callback['_overlay']);
        }
        $first = ($weight === true);
        $weight = $weight === true ? 0 : abs((int)$weight);
    
        $events = isset(self::$events[$name][$weight]) && !$overlay ? self::$events[$name][$weight] : [];
        if ($first) {
            array_unshift($events, $callback);
        } else {
            $events[] = $callback;
        }
        
        self::$events[$name][$weight] = $events;
    }
    
    /**
     * @param     $name
     * @param int|bool $weight 0,1, true
     *
     * @return bool
     */
    public static function off($name, $weight = 0)
    {
        if (!isset(self::$events[$name])) {
            return true;
        }
        if ($weight === true) {
            unset(self::$events[$name]);
        } else {
            unset(self::$events[$name][$weight]);
        }
        
        return true;
    }
    
    /**
     * @param array $events
     */
    public static function import($events = [])
    {
        foreach($events as $name => $callbacks) {
            foreach((array)$callbacks as $callback) {
                self::on($name, $callback);
            }
        }
    }
    
    /**
     * @param string $name
     *
     * @return array|mixed
     */
    public static function get($name = '')
    {
        if (empty($name)) {
            return self::$events;
        }
        
        return isset(self::$events[$name]) ? self::$events[$name] : [];
    }
    
    /**
     * @param      $name
     * @param null $params
     * @param null $extra
     * @param bool $once
     *
     * @return array|mixed
     */
    public static function listen($name, &$params = null, $extra = null, $once = false)
    {
        $results = [];
        if (empty($name)) {
            return $results;
        }
        foreach(self::get($name) as $key => $value) {
            foreach($value as $k => $callback) {
                $results[$k] = self::exec($callback, '', $params, $extra);
                if (false === $results[$k] || (!is_null($results[$k]) && $once)) {
                    break 2;
                }
            }
        }
        
        return $once ? end($results) : $results;
    }
    
    public static function exec($class, $method = '', &$params=null, $extra = null)
    {
        if ($class instanceof \Closure || strpos($class, '::')) {
            $result = call_user_func_array($class, [ &$params, $extra]);
        } elseif (is_array($class)) {
            list($class, $method) = $class;
            $class = is_object($class) ? $class : (new $class());
            $result = $class->{$method}($params, $extra);
        } else {
            $class = is_object($class) ? $class : (new $class());
            $method = (!empty($method) && is_callable([$class, $method])) ? $method : 'run';
            $result = $class->{$method}($params, $extra);
        }
        
        return $result;
    }
}
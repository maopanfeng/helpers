<?php

namespace mxhei\helpers;

use ArrayAccess;
use Countable;
use JsonSerializable;
use IteratorAggregate;
use Serializable;

class Collection implements ArrayAccess,Countable,JsonSerializable,IteratorAggregate,Serializable
{
    /**
     * Collection Data
     *
     * @var array $items
     */
    protected $items = [];
    
    /**
     * Collection constructor.
     *
     * @param array|\Traversable $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
    
    /**
     * @param array $data
     *
     * @return \mxhei\helpers\Collection
     */
    public static function make($data = [])
    {
        return new self($data);
    }
    
    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        Arr::set($this->items, $key, $value);
    }
    
    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->items, $key, $default);
    }
    
    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return !is_null(Arr::get($this->items, $key));
    }
    
    /**
     * @param $key
     */
    public function remove($key)
    {
        Arr::remove($this->items, $key);
    }
    
    /**
     * get all data
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
    
    /**
     * @param $data
     *
     * @return array
     */
    public function merge($data)
    {
        $args = func_get_args();
        $end = end($args);
        $recursive = false;
        if (is_bool($end)) {
            $recursive = $end;
            array_pop($args);
        }
        if (empty($args)) {
            return $this->all();
        }
        
        if ($recursive) {
            array_unshift($args, $this->items);
            $this->items = call_user_func_array('Arr::mergeRecursive', $args);
        } else {
            foreach($args as $items) {
                foreach ($items as $key => $value) {
                    $this->set($key, $value);
                }
            }
        }
    
        return $this->all();
    }
    
    /**
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        Arr::set($this->items, $key, $value);
    }
    
    /**
     * first item
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }
    
    /**
     * last item
     *
     * @return mixed
     */
    public function last()
    {
        $item = end($this->items);
        reset($this->items);
        
        return $item;
    }
    
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    
    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }
    
    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }
    
    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    
    /**
     * serialize collection data
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->items);
    }
    
    /**
     * @param string $serialized
     *
     * @return mixed|void
     */
    public function unserialize($serialized)
    {
        return $this->items = unserialize($serialized);
    }
    
    /**
     * count data
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }
    
    /**
     * get iterator
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
    
    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->items;
    }
    
    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
    
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
    
    public function __unset($name)
    {
        $this->remove($name);
    }
    
    public function __isset($name)
    {
        return $this->has($name);
    }
}
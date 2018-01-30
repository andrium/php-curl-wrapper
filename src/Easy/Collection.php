<?php

namespace andrium\CURL\Easy;

use andrium\CURL;

/**
 * Class Easy\Collection
 * @package andrium\CURL
 */
class Collection implements \ArrayAccess, \Iterator
{
    /**
     * @var CURL\Easy[]
     */
    private $collection = [];

    /**
     * Add request to collection
     * @param mixed $name
     * @param CURL\Easy $request
     */
    protected function add($name, CURL\Easy $request)
    {
        if ($name === null) {
            $this->collection[] = $request;
        } else {
            $this->collection[$name] = $request;
        }
    }

    /**
     * Remove request from collection
     * @param mixed $name
     */
    protected function remove($name)
    {
        unset($this->collection[$name]);
    }

    /**
     * Whether a request exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return bool
     */
    final public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->collection);
    }

    /**
     * Get request
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return CURL\Easy
     */
    final public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * Set request
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset
     * @param CURL\Easy $value
     * @return void
     */
    final public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * Unset request
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     */
    final public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Return the current request
     * @link http://php.net/manual/en/iterator.current.php
     * @return CURL\Easy
     */
    final public function current()
    {
        return current($this->collection);
    }

    /**
     * Move forward to next request
     * @link http://php.net/manual/en/iterator.next.php
     * @return void
     */
    final public function next()
    {
        next($this->collection);
    }

    /**
     * Return the key of the current request
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed
     */
    final public function key()
    {
        return key($this->collection);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return bool
     */
    final public function valid()
    {
        return key($this->collection) !== null;
    }

    /**
     * Rewind the collection to the first request
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void
     */
    final public function rewind()
    {
        reset($this->collection);
    }
}

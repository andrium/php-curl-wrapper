<?php

namespace andrium\CURL;

/**
 * Class Multi
 * @package andrium\CURL
 * @author Andrey Kroshkin <andrium@ya.ru>
 */
class Multi extends HandleContainer implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * CURL Easy Handles
     * @var Easy[]
     */
    private $collection = [];

    /**
     * Initialize CURL Multi Handle
     * @param array $options - An array specifying which options to set and their values
     */
    public function __construct(array $options = [])
    {
        $this->handle = curl_multi_init();
        $this->setOptions($options);
    }

    /**
     * Remove all added CURL Easy Handles and close CURL Multi Handle
     */
    public function __destruct()
    {
        foreach ($this->collection as $easy) {
            curl_multi_remove_handle($this->handle, $easy->handle);
        }

        curl_multi_close($this->handle);
    }

    /**
     * Set an option for a CURL Multi Handle
     * @param int $option - One of CURLMOPT_* constant
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        curl_multi_setopt($this->handle, $option, $value);

        return $this;
    }

    /**
     * Set multiple options for a CURL Multi Handle
     * @param array $options - An array specifying which options to set and their values
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * Run the requests of the collection
     * @return void
     * @throws Multi\Error
     */
    public function execute()
    {
        do {
            $err_code = curl_multi_exec($this->handle, $status);
        } while ($err_code === CURLM_CALL_MULTI_PERFORM);

        while ($status && $err_code === CURLM_OK) {
            if (curl_multi_select($this->handle) === -1) {
                usleep(1);
            }

            do {
                $err_code = curl_multi_exec($this->handle, $status);
            } while ($err_code === CURLM_CALL_MULTI_PERFORM);
        }

        if ($err_code !== CURLM_OK) {
            throw new Multi\Error(curl_multi_strerror($err_code), $err_code);
        }
    }

    /**
     * Add CURL Easy Handle to the collection
     * @param mixed $name
     * @param Easy $easy
     * @throws Multi\Error
     */
    protected function add($name, Easy $easy)
    {
        if (($errornum = curl_multi_add_handle($this->handle, $easy->handle)) !== CURLM_OK) {
            throw new Multi\Error(curl_multi_strerror($errornum), $errornum);
        }

        if ($name === null) {
            $this->collection[] = $easy;
        } else {
            $this->collection[$name] = $easy;
        }
    }

    /**
     * Remove CURL Easy Handle from the collection
     * @param mixed $name
     * @throws Multi\Error
     */
    protected function remove($name)
    {
        if (($errornum = curl_multi_remove_handle($this->handle, $this->collection[$name]->handle)) !== CURLM_OK) {
            throw new Multi\Error(curl_multi_strerror($errornum), $errornum);
        }

        unset($this->collection[$name]);
    }

    /**
     * Whether an offset exists
     * @link http://php.net/manual/arrayaccess.offsetexists.php
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->collection);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/arrayaccess.offsetget.php
     * @param mixed $offset
     * @return Easy
     */
    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * Assign a value to the specified offset
     * @link http://php.net/manual/arrayaccess.offsetset.php
     * @param mixed $offset
     * @param Easy $value
     * @return void
     * @throws Multi\Error
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * Unset an offset
     * @link http://php.net/manual/arrayaccess.offsetunset.php
     * @param mixed $offset
     * @return void
     * @throws Multi\Error
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/iterator.current.php
     * @return Easy
     */
    public function current()
    {
        return current($this->collection);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/iterator.next.php
     * @return void
     */
    public function next()
    {
        next($this->collection);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed
     */
    public function key()
    {
        return key($this->collection);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/iterator.valid.php
     * @return bool
     */
    public function valid()
    {
        return key($this->collection) !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/iterator.rewind.php
     * @return void
     */
    public function rewind()
    {
        reset($this->collection);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/countable.count.php
     * @return int
     */
    public function count()
    {
        return count($this->collection);
    }
}

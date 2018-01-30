<?php

namespace andrium\CURL;

/**
 * Class Easy
 * @package andrium\CURL
 * @author Andrey Kroshkin <andrium@ya.ru>
 */
class Easy
{
    /**
     * CURL Easy Handle resource
     * @var resource
     */
    private $handle;

    /**
     * Initialize CURL Easy Handle
     * @param array $options - An array specifying which options to set and their values
     */
    public function __construct(array $options = [])
    {
        $this->handle = curl_init();
        $this->setOptions($options);
    }

    /**
     * Close CURL Easy Handle
     */
    public function __destruct()
    {
        curl_close($this->handle);
    }

    /**
     * Copy CURL Easy Handle
     */
    public function __clone()
    {
        $this->handle = curl_copy_handle($this->handle);
    }

    /**
     * Set an option for a CURL Easy Handle
     * @param int $option - One of CURLOPT_* constant
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        if ($option === CURLOPT_SHARE && $value instanceof Share) {
            curl_setopt($this->handle, $option, $value->getHandle());
        } else {
            curl_setopt($this->handle, $option, $value);
        }

        return $this;
    }

    /**
     * Set multiple options for a CURL Easy Handle
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
     * Get CURL Easy Handle resource
     * @return resource - CURL Easy Handle resource
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get error a transfer
     * @return null | string
     */
    public function getError()
    {
        if (!empty($error = curl_error($this->handle))) {
            return $error;
        }

        return null;
    }

    /**
     * Get information a transfer
     * @param int $option - One of CURLINFO_* constant
     * @return mixed
     */
    public function getInfo($option)
    {
        return curl_getinfo($this->handle, $option);
    }

    /**
     * Get content a transfer
     * @return string
     */
    public function getContent()
    {
        return curl_multi_getcontent($this->handle);
    }

    /**
     * Perform a CURL Easy Handle
     * @return mixed
     * @throws Easy\Error
     */
    public function execute()
    {
        if (($content = curl_exec($this->handle)) === false) {
            throw new Easy\Error(curl_error($this->handle), curl_errno($this->handle));
        }

        return $content;
    }

    /**
     * Reset all options of a CURL Easy Handle
     * @return $this
     */
    public function reset()
    {
        curl_reset($this->handle);

        return $this;
    }
}

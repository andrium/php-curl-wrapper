<?php

namespace andrium\CURL;

/**
 * Class Multi
 * @package andrium\CURL
 * @author Andrey Kroshkin <andrium@ya.ru>
 */
class Multi extends Easy\Collection
{
    /**
     * CURL Multi Handle resource
     * @var resource
     */
    private $handle;

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
        foreach ($this as $request) {
            curl_multi_remove_handle($this->handle, $request->getHandle());
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
     * @return $this
     * @throws Multi\Error
     */
    public function execute()
    {
        do {
            $errornum = curl_multi_exec($this->handle, $still_running);
        } while ($still_running);

        if ($errornum !== CURLM_OK) {
            throw new Multi\Error(curl_multi_strerror($errornum), $errornum);
        }

        return $this;
    }

    /**
     * Add request to collection
     * @param mixed $name
     * @param Easy $request
     * @throws Multi\Error
     */
    protected function add($name, Easy $request)
    {
        if (($errornum = curl_multi_add_handle($this->handle, $request->getHandle())) !== CURLM_OK) {
            throw new Multi\Error(curl_multi_strerror($errornum), $errornum);
        }
        parent::add($name, $request);
    }

    /**
     * Remove request from collection
     * @param mixed $name
     * @throws Multi\Error
     */
    protected function remove($name)
    {
        if (($errornum = curl_multi_remove_handle($this->handle, $this[$name]->getHandle())) !== CURLM_OK) {
            throw new Multi\Error(curl_multi_strerror($errornum), $errornum);
        }
        parent::remove($name);
    }
}

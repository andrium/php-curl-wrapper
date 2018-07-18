<?php

namespace andrium\CURL;

/**
 * Class Share
 * @package andrium\CURL
 * @author Andrey Kroshkin <andrium@ya.ru>
 */
class Share extends AbstractHandleContainer
{
    /**
     * Initialize CURL Share Handle
     * @param array $options - An array specifying which options to set and their values
     */
    public function __construct(array $options = [])
    {
        $this->handle = curl_share_init();
        $this->setOptions($options);
    }

    /**
     * Close CURL Share Handle
     */
    public function __destruct()
    {
        curl_share_close($this->handle);
    }

    /**
     * Set an option for a CURL Share Handle
     * @param int $option - One of CURLSHOPT_* constant
     * @param mixed $value - One of CURL_LOCK_DATA_* constant
     * @return $this
     */
    public function setOption($option, $value)
    {
        curl_share_setopt($this->handle, $option, $value);

        return $this;
    }

    /**
     * Set multiple options for a CURL Share Handle
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
}

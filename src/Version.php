<?php

namespace andrium\CURL;

/**
 * Class Version
 * @package andrium\CURL
 * @author Andrey Kroshkin <andrium@ya.ru>
 */
class Version
{
    /**
     * Get CURL version
     * @return string
     */
    public static function getVersion()
    {
        return curl_version()['version'];
    }

    /**
     * Has CURL protocol
     * @param string $protocol - Protocol name in lowercase
     * @return bool
     */
    public static function hasProtocol($protocol)
    {
        return in_array($protocol, curl_version()['protocols']);
    }

    /**
     * Has CURL feature
     * @param int $feature - One of CURL_VERSION_* constant
     * @return bool
     */
    public static function hasFeature($feature)
    {
        return curl_version()['features'] & $feature;
    }
}

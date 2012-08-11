<?php

class Hobis_Api_Cache_Key
{
    const SEPARATOR = '_';

    const STATUS_HIT    = 'hit';
    const STATUS_MISS   = 'miss';

    const LOG_NAME_STATUS   = 'cacheKeyStatus';
    const LOG_URI_STATUS    = '/var/log/cache/keyStatus.log';

    /**
     * TTL for a key
     *
     * @var int
     */
    protected $expiry;

    /**
     * How the value will be accessed
     *
     * @var string
     */
    protected $key;

    /**
     * Value related to a specific key
     *
     * @var string
     */
    protected $value;

    /**
     * Setter for expiry
     *
     * @param int
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
    }

    /**
     * Setter for key
     *
     * @param string
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Setter for value
     *
     * @param string
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Getter for expiry
     *
     * @return int
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Getter for key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Getter for value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
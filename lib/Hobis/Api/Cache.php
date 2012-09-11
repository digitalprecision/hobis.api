<?php

class Hobis_Api_Cache extends Memcached
{
    const TYPE_VOLATILE      = 1;
    const TYPE_PERSISTENT    = 2;

    const EXPIRY_DEFAULT    = 3600;
    const EXPIRY_MIN        = 10;

    /**
     * Wrapper method overriding default behavior for getting a cache value
     *
     * @param string
     * @param callable
     * @param float
     * @return mixed
     * @throws Hobis_Api_Exception
     */
    public function get($key, $cacheCallback = null, &$casToken = null)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($key)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $key: %s', $key));
        }

        $cachedValue = parent::get($key, $cacheCallback, $casToken);

        $hitStatus = (false === $cachedValue) ? Hobis_Api_Cache_Key::STATUS_MISS : Hobis_Api_Cache_Key::STATUS_HIT;

        Hobis_Api_Cache_Key_Package::toStatusLog()->debug(sprintf('Action: get | CacheKey: %s | Status: %s | Result Message: %s', $key, $hitStatus, $this->getResultMessage()), Hobis_Api_Log::DEBUG_ON_PROD_OVERRIDE);

        return $cachedValue;
    }

    /**
     * Wrapper method for deleting an item from cache
     *
     * @param string
     * @param int
     */
    public function delete($key, $time = 0)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($key)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $key: %s', $key));
        }

        $deleteStatus = parent::delete($key, $time);

        $hitStatus = (false === $deleteStatus) ? Hobis_Api_Cache_Key::STATUS_MISS : Hobis_Api_Cache_Key::STATUS_HIT;

        Hobis_Api_Cache_Key_Package::toStatusLog()->debug(sprintf('Action: delete | CacheKey: %s | Status: %s | Result Message: %s', $key, $hitStatus, $this->getResultMessage()), Hobis_Api_Log::DEBUG_ON_PROD_OVERRIDE);

        return $deleteStatus;
    }

    /**
     * Wrapper method overriding default behavior for setting a cache value
     *
     * @param string
     * @param mixed
     * @param int
     * @throws Hobis_Api_Exception
     */
    public function set($key, $value, $expiry = self::EXPIRY_DEFAULT)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_String_Package::populated($key)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $key: %s', $key));
        } elseif (!Hobis_Api_String_Package::populatedNumeric($expiry)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $expiry: %d', $expiry));
        } elseif ($expiry < self::EXPIRY_MIN) {
            throw new Hobis_Api_Exception(sprintf('Invalid $expiry: %d, must be > %d', $expiry, self::EXPIRY_MIN));
        } elseif ((!Hobis_Api_Array_Package::populated($value)) &&
            (!Hobis_Api_String_Package::populated($value))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $value: %s', serialize($value)));
        }
        //-----

        Hobis_Api_Cache_Key_Package::validate($key);

        return parent::set($key, $value, $expiry);
    }
}
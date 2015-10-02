<?php

class Hobis_Api_Cache extends Memcached
{
    const TYPE_VOLATILE      = 1;
    const TYPE_PERSISTENT    = 2;

    const EXPIRY_DEFAULT    = 3600;
    const EXPIRY_MIN        = 10;
    
    /**
     * Magic method override so we can use our version of get/set
     *	Otherwise default get/set will break in php 5.5+
     *
     * @param string
     * @param array
     */
    public function __call($name, $arguments)
    {
        switch ($name)
        {
            // Override due to 5.5+ using php-memcache 2.2 and morons not fixing it
            case 'get':
            
                $this->myGet($arguments);
            
                break;
            
            // Override due to 5.5+ using php-memcache 2.2 and morons not fixing it
            case 'set':
            
                $this->mySet($arguments);
                
                break;
        }
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
            throw new Hobis_Api_Exception(sprintf('Invalid $key: %s', serialize($key)));
        }

        $deleteStatus = parent::delete($key, $time);

        $hitStatus = (false === $deleteStatus) ? Hobis_Api_Cache_Key::STATUS_MISS : Hobis_Api_Cache_Key::STATUS_HIT;

        Hobis_Api_Cache_Key_Package::toStatusLog()->debug(sprintf('Action: delete | CacheKey: %s | Status: %s | Time: %s | Result Message: %s', $key, $hitStatus, serialize($time), $this->getResultMessage()));

        return $deleteStatus;
    }
	
	/**
	 * Wrapper method to ease calling code footprint
	 * 
	 * @param object
	 */
	public function easySet(Hobis_Api_Cache_Key $key)
	{
		return $this->set($key->toString(), $key->getValue(), $key->getExpiry());
	}

    /**
     * Wrapper method overriding default behavior for getting a cache value
     *
     * @param string
     * @param callable
     * @param float
     * @return mixed
     * @throws Hobis_Api_Exception
     */
    protected function myGet($key, $cacheCallback = null, &$casToken = null, &$udf_flags = NULL)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($key)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $key: %s', $key));
        }

        $cachedValue = parent::get($key, $cacheCallback, $casToken);

        $hitStatus = (false === $cachedValue) ? Hobis_Api_Cache_Key::STATUS_MISS : Hobis_Api_Cache_Key::STATUS_HIT;

        Hobis_Api_Cache_Key_Package::toStatusLog()->debug(sprintf('Action: get | CacheKey: %s | Status: %s | Result Message: %s', $key, $hitStatus, $this->getResultMessage()));

        return $cachedValue;
    }    

    /**
     * Wrapper method overriding default behavior for setting a cache value
     *
     * @param string
     * @param mixed
     * @param int
     * @throws Hobis_Api_Exception
     */
    protected function mySet($key, $value, $expiry = self::EXPIRY_DEFAULT, &$udf_flags = NULL)
    {
        //-----
        // Validate
        //-----
        if (false === Hobis_Api_String_Package::populated($key)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $key: %s', serialize($key)));
        } elseif (false === Hobis_Api_String_Package::populatedNumeric($expiry)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $expiry: %s', seralize($expiry)));
        } elseif ($expiry < self::EXPIRY_MIN) {
            throw new Hobis_Api_Exception(sprintf('Invalid $expiry: %s, must be > %d', serialize($expiry), self::EXPIRY_MIN));
        } 
        elseif ((false === Hobis_Api_Array_Package::populated($value)) &&
            	(false === Hobis_Api_String_Package::populated($value))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $value: %s', serialize($value)));
        }
        //-----

        Hobis_Api_Cache_Key_Package::validate($key);

        return parent::set($key, $value, $expiry);
    }
}
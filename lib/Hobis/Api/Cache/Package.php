<?php

class Hobis_Api_Cache_Package
{
    /**
     * Container for prepared cache objects
     *
     * @var array
     */
    protected static $caches;

    /**
     * Container for valid cache types
     *
     * @var array
     */
    protected static $validTypes = array(
        Hobis_Api_Cache::TYPE_PERSISTENT,
        Hobis_Api_Cache::TYPE_VOLATILE
    );

    /**
     * Wrapper method for deleting a key from cache store
     *
     * @param Hobis_Api_Cache_Key
     * @param Hobis_Api_Cache
     * @return type
     */
    public static function delete(Hobis_Api_Cache_Key $key, $type = Hobis_Api_Cache::TYPE_VOLATILE)
    {
        //Validate
        if (!in_array($type, self::$validTypes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $type: %s', $type));
        }

        return self::factory($type)->delete($key->getKey());
    }

    /**
     * Factory method for creating cache objects
     *
     * @param string
     * @return object
     * @throws Hobis_Api_Exception
     */
    public static function factory($type)
    {
        // Validate
        if (!in_array($type, self::$validTypes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $type: %s', $type));
        }

        // Attempt to use singleton
        if (true === Hobis_Api_Array_Package::populatedKey($type, self::$caches)) {
            return self::$caches[$type];
        }

        // Load config
        $settings = sfYaml::load(self::getConfig());

        if ((!Hobis_Api_Array_Package::populatedKey($type, $settings)) ||
            (!Hobis_Api_Array_Package::populatedKey('servers', $settings[$type]))) {
        	throw new Hobis_Api_Exception(sprintf('Invalid $settings: %s', serialize($settings)));
        }

        // Localize
        $port       = $settings['port'];
        $servers    = $settings[$type]['servers'];

		// So there is a real flaw in using the memcached api, as it assumes that all memcached servers will be up, forever
		//	Obviously fragile, I tried a number of various settings to affect the underlying libmemcached mechanisms of self
		//	healing a cache pool with broken nodes
		//	Cache is meant to be transitory, which is fine, but I prefer caching sessions rather than storing in a more costly store such as RDMBS
		//	And with a true cache pool, this is a viable solution, as there should be at least 2 nodes participating in the pool to offer some
		//	fault tolerance
		//	Point is, if a pool node goes down, and the key is unattainable, no problem, handle the miss and store it on another server
		//	Not complicated right?
		//	The test cache object allows us to add all the pool node, then derive whether the node is available or not via getStats() and checking
		//	if pid > 0
		//	Then, from the servers determined to be responding, we add them to the real cache object, which is what we return out of this factory
		$cacheReal = new Hobis_Api_Cache;
        $cacheTest = new Hobis_Api_Cache;
		
		// If there are options present, lets set them
		if (true === Hobis_Api_Array_Package::populatedKey('options', $settings[$type])) {
			
			$options = array();
			
			foreach ($settings[$type]['options'] as $optionPair) {
				
				list($key, $value) = array_map('trim', explode(':', $optionPair));
				
				if (true === is_numeric($value)) {
					$value = (int) $value;
				}
				
				$cacheReal->setOption($key, $value);
			}
		}

		// Granted we are using a singleton, so shouldn't reach this point for every call, however it's better safe than sorry
		if (count($cacheTest->getServerList()) < 1) {
			
			$knownGoodServers	= array();
			$serversToAdd		= array();
			
	        foreach ($servers as $server) {	        	
				$serversToAdd[] = array($server, $port);
	        }
			
			$cacheTest->addServers($serversToAdd);
			
			foreach ($cacheTest->getStats() as $server => $stats) {
				
				// Test if server is actually available
				if ((false === Hobis_Api_Array_Package::populatedKey('pid', $stats)) ||
					($stats['pid'] < 0)) {
					continue;
				}
					
				$knownGoodServers[] = $server;
			}
			
			// It is possible that entire cache pool took a dump
			if (true === Hobis_Api_Array_Package::populated($knownGoodServers)) {
				
				$serversToAdd = array();
				
				foreach ($knownGoodServers as $server) {
					
					list($host, $port) = array_map('trim', explode(':', $server));
					
					$serversToAdd[] = array($host, (int) $port);
				}
				
				if (true === Hobis_Api_Array_Package::populated($serversToAdd)) {
					$cacheReal->addServers($serversToAdd);
				}
			}
		}

        self::$caches[$type] = $cacheReal;

        return self::$caches[$type];
    }

    /**
     * Wrapper method for retrieving a value from cache store
     *
     * @param Hobis_Api_Cache_Key
     * @param Hobis_Api_Cache
     * @return type
     */
    public static function get(Hobis_Api_Cache_Key $key, $type = Hobis_Api_Cache::TYPE_VOLATILE)
    {
        //Validate
        if (!in_array($type, self::$validTypes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $type: %s', $type));
        }

        return self::factory($type)->get($key->getKey());
    }

    /**
	 * Convience method for getting config file
	 *
	 * @return string
	 */
	protected static function getConfig()
	{
        return Hobis_Api_Directory_Package::fromArray(
            array(
                Hobis_Api_Environment_Package::getAppConfigPath(),
                'cache',
                'config.yml'
            )
        );
	}

    /**
     * Wrapper method for storing value within cache store
     *
     * @param object
     * @param object
     */
    public static function set(Hobis_Api_Cache_Key $key, $type = Hobis_Api_Cache::TYPE_VOLATILE)
    {
        //Validate
        if (!in_array($type, self::$validTypes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $type: %s', $type));
        }

        return self::factory($type)->set($key->getKey(), $key->getValue(), $key->getExpiry());
    }
}
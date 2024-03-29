<?php

class Hobis_Api_Cache_Package
{
	/**
	 * Container for cache objects associated to specific namespaces
	 * 	These objects are purposely associated to specific namespaces so we can incorporate various option settings without collision
	 */
	protected static $cachesByNamespace;
	
    /**
     * Container for cache objects by specific type
	 * 	This allows us to use cache object without having to re-init a new cache object every time
     *
     * @var array
     */
    protected static $cachesByType;

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
	 * @param string | Used to allow multiple cache types to be segragated so they can carry boundaried options. One cache object may have differing options than another cache object
     * @return object
     * @throws Hobis_Api_Exception
     */
    public static function factory($type, $namespace)
    {
    	//-----
        // Validate
        //-----
        if (false === in_array($type, self::$validTypes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $type: %s', serialize($type)));
        } elseif (false === Hobis_Api_String_Package::populated($namespace)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $namespace: %s', serialize($type)));
        }
		//-----

        // Attempt to locate pre-existing cache object associated to given namespace
        if (true === isset(self::$cachesByNamespace[$namespace])) {
            return self::$cachesByNamespace[$namespace];
        }
		
		// If no cache object could be found by namespace, lets check to see if we have cache object by type, this will allow us to
		//	associate a cache object to namespace without having to re-init the primary cache object
		elseif (true === isset(self::$cachesByType[$type])) {
			
			self::$cachesByNamespace[$namespace] = self::$cachesByType[$type];
			
            return self::$cachesByNamespace[$namespace];
        }
		
		//-----
		// No cache object by namespace or type could be found, let's init one
		//-----
		
        // Load config
        $settings = sfYaml::load(self::getConfig());

        if ((!Hobis_Api_Array_Package::populatedKey($type, $settings)) ||
            (!Hobis_Api_Array_Package::populatedKey('servers', $settings[$type]))) {
        	throw new Hobis_Api_Exception(sprintf('Invalid $settings: %s', serialize($settings)));
        }

        // Localize
        $port       = (int) $settings['port'];
        $servers    = $settings[$type]['servers'];
		
		$servers = array_map('trim', $servers);
		
		$cache = new Hobis_Api_Cache;
		
		foreach ($servers as $server) {
			
			$serversToAdd[] = array($server, $port);
		}				
				
		$cache->addServers($serversToAdd);
		
		self::$cachesByNamespace[$namespace]	= $cache;
		self::$cachesByType[$type]				= $cache;
		//-----

        return self::$cachesByNamespace[$namespace];		
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
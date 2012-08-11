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
        if (Hobis_Api_Array_Package::populatedKey($type, self::$caches)) {
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

        $cache = new Hobis_Api_Cache();

        foreach ($servers as $server) {
            $cache->addServer($server, $port);
        }

        self::$caches[$type] = $cache;

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
        return substr(__FILE__, 0, strpos(__FILE__, '/lib')) . '/etc/cache/connection.yml';
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
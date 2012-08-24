<?php

/**
 * Wrapper class for Zend DB
 */
class Hobis_Api_Database_Package
{
    /**
     * Singleton for dbos
     *
     * @var array
     */
    public static $dbos = array();

    /**
     * Factory method for creating database objects
     *
     * @param string $dbName
     * @param string $context
     * @throws Hobis_Api_Exception
     */
	public static function getDbo($name, $context = Hobis_Api_Database::CONTEXT_READONLY)
	{
	    //-----
	    // Validate
	    //-----
	    if (!Hobis_Api_String_Package::populated($name)) {
	        throw new Hobis_Api_Exception('Invalid $name');
	    }

	    elseif (!Hobis_Api_String_Package::populated($context)) {
	        throw new Hobis_Api_Exception('Invalid $context');
	    }
	    //-----

        // Build hash
        $hash = md5($name . '_' . $context);

        // Attempt to use singleton
        if (Hobis_Api_Array_Package::populatedKey($hash, self::$dbos)) {
            return self::$dbos[$hash];
        }

        // Load settings defined in config file
        $settings = sfYaml::load(self::getConfig());

        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey($name, $settings)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $dbName: %s', serialize($settings)));
        } elseif (!Hobis_Api_Array_Package::populatedKey($context, $settings[$name])) {
            throw new Hobis_Api_Exception(sprintf('Invalid $context: %s', serialize($settings)));
        }
        //-----

        // Kiss
        $params = $settings[$name][$context]['database'];

        //-----
        // set adapter specific, global attributes
        //-----
        switch ($params['adapter']) {

            case Hobis_Api_Database::ADAPTER_PDO:

                $params['driver_options'] = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );

                break;
        }
        //-----

        $dbo = Zend_Db::factory($params['adapter'], $params);

        self::$dbos[$hash] = $dbo;
        //-----

        return $dbo;
	}

	/**
	 * Convience method for getting database yaml file
	 *
	 * @return path
	 */
	protected static function getConfig()
	{
        return Hobis_Api_Directory_Package::fromArray(
            array(
                Hobis_Api_Environment_Package::getAppConfigPath(),
                'database',
                'config.yml'
            )
        );
	}
}
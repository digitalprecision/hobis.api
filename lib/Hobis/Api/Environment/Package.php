<?php

class Hobis_Api_Environment_Package
{
    /**
     * Singleton for application level config path
     *  This is needed so we can access app level specific configuration settings
     *  such as database.yml
     *
     * @var string
     */
    protected static $appConfigPath = null;

    /**
     * Setter for appEtcPath singleton
     *
     * @param string
     */
    public static function setAppConfigPath($path)
    {
        self::$appConfigPath = $path;
    }

    /**
     * Getter for appEtcPath singleton
     *
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function getAppConfigPath()
    {
        if (is_null(self::$appConfigPath)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $appConfigPath: %s', self::$appConfigPath));
        }

        return self::$appConfigPath;
    }

    /**
     * Wrapper method for getting environment value
     *
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function getValue($variable)
    {
        // Validate
        if (false === Hobis_Api_String_Package::populated($variable)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $variable: (%s)', serialize($variable)));
        }        

        $value = getenv($variable);

        // Setting default environment can be dangerous, id prefer if calling code just skip code blocks if enviornment could not be determined
        //  vs. acting as if a valid env was set
        if (false === Hobis_Api_String_Package::populated($value)) {
            throw new Hobis_Api_Exception(sprintf('Environment variable (%s) has no value matching (%s)', $variable, $value));
        }

        return $value;
    }

    /**
     * Convience method for getting database yaml file
     *
     * @return path
     */
    protected static function getSourceConfigFilename()
    {
        return substr(__FILE__, 0, strpos(__FILE__, '/lib')) . '/etc/system/variable/config.yml';
    }

    /**
     * Wrapper method for determining if current execution is via cli
     *
     * @return bool
     */
    public static function isCli()
    {
        return (php_sapi_name() === 'cli') ? true : false;
    }
	
	/**
	 * Wrapper method for determining if current env is dev
	 * 
	 * @return bool
	 */
	public static function isDev()
	{
		return (self::getValue(Hobis_Api_Environment::VAR_LABEL_SERVICE) === Hobis_Api_Environment::DEV) ? true : false;
	}

    /**
     * Wrapper method for determing if we are on a linux os
     *
     * @return bool
     */
    public static function isLinuxOs()
    {
        return (stripos(php_uname(), 'linux') !== false) ? true : false;
    }
	
	
}
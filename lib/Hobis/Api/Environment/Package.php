<?php

class Hobis_Api_Environment_Package
{
    /**
     * Singleton for application level etc path
     *  This is needed so we can access app level specific configuration settings
     *  such as database.yml
     *
     * @var string
     */
    protected static $appEtcPath = null;

    /**
     * Setter for appEtcPath singleton
     *
     * @param string
     */
    public static function setAppEtcPath($path)
    {
        self::$appEtcPath = $path;
    }

    /**
     * Getter for appEtcPath singleton
     *
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function getAppEtcPath()
    {
        if (is_null(self::$appEtcPath)) {
            throw new Hobis_Api_Exception(sprintf('Invalid appEtcPath: %s', self::$appEtcPath));
        }

        return self::$appEtcPath;
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
        if (!Hobis_Api_String_Package::populated($variable)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $variable: (%s)', serialize($variable)));
        }

        // !!CACHEME

        // Load variables from config
        $variables = sfYaml::load(self::getSourceConfigFilename());

        // Validate
        if (!Hobis_Api_Array_Package::populatedKey($variable, $variables)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $variable: (%s)', $variable));
        }

        $value = getenv($variable);

        // Setting default environment can be dangerous, id prefer if calling code just skip code blocks if enviornment could not be determined
        //  vs. acting as if a valid env was set
        if (!Hobis_Api_String_Package::populated($value)) {
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
}
<?php

class Hobis_Api_Environment_Server_Package
{
    /**
     * Wrapper method for determining if php is running in cli mode
     *
     * return bool
     */
    public static function isCli()
    {
        return (bool) (PHP_SAPI === 'cli');
    }

    /**
     * Wrapper method for determining if server is flagged as dev
     *
     * @return bool
     */
    public static function isDev()
    {
        return (Hobis_Api_Environment_Package::getValue(Hobis_Api_Environment::VAR_LABEL_SERVICE) === Hobis_Api_Environment::DEV);
    }

    /**
     * Wrapper method for determining if server is flagged as prod
     *
     * @return bool
     */
    public static function isProd()
    {
        return (Hobis_Api_Environment_Package::getValue(Hobis_Api_Environment::VAR_LABEL_SERVICE) === Hobis_Api_Environment::PROD);
    }
}

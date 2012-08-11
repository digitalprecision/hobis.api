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
}

<?php

class Hobis_Api_Controller_Package
{
    /**
     * Array of global keys related to ip addresses
     *
     * @var array
     */
    protected static $ipAddressGlobals = array(
        'HTTP_X_REAL_IP',
        'HTTP_X_FORWARDED_FOR'
    );

    /**
     * Array of ip addresses allowed to access dev controllers
     *
     * @var array
     */
    protected static $validIps = array(
        '127.0.0.1',
        '173.58.49.41',
        '192.168.0.254',
        '207.7.104.10',
        '74.100.45.90'
    );

    /**
     * Wrapper method to determine if ipaddress should have access to a dev controller
     *
     * @param array
     * @return bool
     * @throws Hobis_Exception
     */
    public static function allowAccessToDevController(array $serverGlobals)
    {
        // Validate
        if (!Hobis_Api_Array_Package::populated($serverGlobals)) {
            throw new Hobis_Api_Exception('Invalid $serverGlobals');
        }

        $ipIsValid = false;

        // Sites are behind proxy (nginx), so REMOTE_ADDR will always be nginx ip, we need to check diff globals
        foreach (self::$ipAddressGlobals as $ipAddressGlobal) {

            if (!isset($serverGlobals[$ipAddressGlobal])) {
                continue;
            }

            if (in_array($serverGlobals[$ipAddressGlobal], self::$validIps)) {

                $ipIsValid = true;

                break;
            }
        }

        return ($ipIsValid) ? true : false;
    }

    /**
     * Wrapper method for issuing a 503 for a request
     *  This is useful if the site cannot be accessed due to low level issue
     */
    public static function issue503()
    {
        header("HTTP/1.1 503 Service Temporarily Unavailable");
        header("Status: 503 Service Temporarily Unavailable");

        exit;
    }
}
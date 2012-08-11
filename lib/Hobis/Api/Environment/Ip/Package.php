<?php

class Hobis_Api_Environment_Ip_Package
{
    const IP_DEV_LOAD_BALANCER      = '192.168.0.1';
    const IP_PROD_LOAD_BALANCER     = '173.255.252.120';
    const IP_V4_LOOPBACK            = '127.0.0.1';
    const IP_V6_LOOPBACK            = '::1';
    const IP_DEV_INTERNAL_SUBNET    = '192.168.100.';
    const IP_PROD_INTERNAL_SUBNET   = '173.255.252.';

    public static $proxyIps = array(
        self::IPV4_HOME_IP
    );

    public static $internalIps = array(
        self::IP_V4_LOOPBACK,
        self::IP_V6_LOOPBACK,
        self::IP_DEV_INTERNAL_SUBNET,
        self::IP_PROD_INTERNAL_SUBNET
    );

    protected static $internalIpRegex;

    /**
     * Wrapper method for determining remote ip address
     *
     * @return string
     */
    public static function getRemoteIp(array $server)
    {
        if (!Hobis_Api_Array_Package::populated($server)) {
            throw new Hobis_Api_Exception('Invalid $server');
        }

        if ((Hobis_Api_Array_Package::populatedKey('REMOTE_ADDR', $server)) &&
            (Hobis_Api_Array_Package::populatedKey('HTTP_X_FORWARDED_FOR', $server)) &&
            (in_array($server['REMOTE_ADDR'], self::$proxyIps))) {
            return $server['HTTP_X_FORWARDED_FOR'];
        }

        if (Hobis_Api_Array_Package::populatedKey('REMOTE_ADDR', $server)) {
            return $server['REMOTE_ADDR'];
        }

        return null;
    }

    /**
     * Returns true if the the remote IP is from an internal server, false otherwise
     */
    public static function isInternalRequest()
    {
        return (bool) preg_match(self::getInternalIpRegex(), self::getRemoteIP());
    }    

    /**
     * Wrapper method for getting http host
     *
     * @param array $server
     * @return mixed (string|null)
     * @throws Hobis_Api_Exception
     */
    public static function getHttpHost(array $server)
    {
        if (Hobis_Api_Environment_Server_Package::isCli()){
            throw new Hobis_Api_Exception('Cannot Retrieve Http Host in Command Line Mode');
        }

        return (Hobis_Api_Array_Package::populatedKey('HTTP_HOST', $server)) ? $server['HTTP_HOST'] : null;
    }
   
    /**
     * Wrapper method for setting internal ip regex
     *
     * @return string
     */
    private static function getInternalIpRegex()
    {
        if (!Hobis_Api_String_Package::populated(self::$internalIpRegex)) {
            self::$internalIpRegex = "/^(" . str_replace('.', '\.', implode('|',self::$internalIps)) . ")/";
        }

        return self::$internalIpRegex;
    }    
}
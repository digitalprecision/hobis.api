<?php

class Hobis_Api_IP_Package
{
    /**
     * Wrapper method for determining if ip is ipv4
     *
     * @param mixed
     * @return bool
     * @throws Hobis_Api_Exception
     */
    public static function isIPv4($ip)
    {
        // Validate
        if (false === Hobis_Api_String_Package::populated($ip)) {
            
            throw new Hobis_Api_Exception(sprintf('Invalid $ip: %s', serialize($ip)));
        }
        
        return (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) ? true : false;
    }
    
    /**
     * Wrapper method for determining if ip is ipv6
     *
     * @param mixed
     * @return bool
     * @throws Hobis_Api_Exception
     */
    public static function isIPv6($ip)
    {
        // Validate
        if (false === Hobis_Api_String_Package::populated($ip)) {
            
            throw new Hobis_Api_Exception(sprintf('Invalid $ip: %s', serialize($ip)));
        }
        
        return (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) ? true : false;
    }
}
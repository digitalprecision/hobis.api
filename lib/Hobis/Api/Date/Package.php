<?php

class Hobis_Api_Date_Package
{
    /**
     * Wrapper method for converting a string to timestamp
     * 
     * @param string
     * @param string
     * @return int
     * @throws Hobis_Api_Exception
     */
    public static function stringToTime($string, $timeZone = 'UTC')
    {
        // Validate
        if (false === Hobis_Api_String_Package::populated($timeZone)) {
            throw new Hobis_Api_Exception(sprintf('Invalid timeZoneId: %s', serialize($timeZone)));
        }
        
        $date = new DateTime($string, new DateTimeZone($timeZone));
        
        return $date->getTimestamp();
    }
}
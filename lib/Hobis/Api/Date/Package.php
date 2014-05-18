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
    public static function stringToTime($string, $timeZone = Hobis_Api_Time_Zone::TOKEN_SHORT_UTC)
    {   
        $date = new DateTime($string, new DateTimeZone($timeZone));
        
        return $date->getTimestamp();
    }
}
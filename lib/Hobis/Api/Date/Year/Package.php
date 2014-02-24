<?php

class Hobis_Api_Date_Year_Package
{
    /**
     * Wrapper method for creating an array of years from given params
     * 
     * @param int
     * @param int
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function toArray($start, $offset = 0)
    {
        //-----
        // Validate
        //-----
        if (false === Hobis_Api_String_Package::populatedNumeric($start)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $start: %s', serialize($start)));
        } elseif (false === Hobis_Api_String_Package::populatedNumeric($offset)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $offset: %s', serialize($offset)));
        }
        
        $years = range($start, (date('Y') + $offset));
        
        return array_combine($years, $years);
    }
}
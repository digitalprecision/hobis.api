<?php

class Hobis_Api_Date_Month_Package
{
    /**
     * Container for months as ints
     * 
     * @var array 
     */
    protected static $monthsAsInts;
    
    /**
     * Container for months as strings
     * 
     * @var array
     */
    protected static $monthsAsStrings;
    
    /**
     * Wrapper method for getting months as ints
     * 
     * @return array
     */
    protected static function getMonthsAsInts()
    {
        if (false === Hobis_Api_Array_Package::populated(self::$monthsAsInts)) {
            self::$monthsAsInts = range(1, 12);
        }
        
        return self::$monthsAsInts;
    }
    
    /**
     * Wrapper method for getting months as strings
     * 
     * @return array
     */
    protected static function getMonthsAsStrings()
    {
        if (false === Hobis_Api_Array_Package::populated(self::$monthsAsStrings)) {
            self::$monthsAsStrings = array(
                'January',
                'Febuary',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            );
        }
        
        return self::$monthsAsStrings;
    }
    
    /**
     * Wrapper method for creating an array of months based on format
     * 
     * @param string
     * @return array
     */
    public static function toArray($format = Hobis_Api_Date_Month::FORMAT_NUMERIC_SHORT)
    {
        switch ($format) {
        
            case Hobis_Api_Date_Month::FORMAT_STRING_LONG:
                $months = self::getMonthsAsStrings();
                break;
            
            case Hobis_Api_Date_Month::FORMAT_STRING_SHORT:
                $months = array_map(function ($element) { return substr($element, 0, 3); }, self::getMonthsAsStrings());
                break;
            
            case Hobis_Api_Date_Month::FORMAT_NUMERIC_LONG:
                $months = array_map(function ($element) { return (strlen($element) < 2) ? sprintf('0%s', $element) : $element; }, self::getMonthsAsInts());
                break;
            
            default:
                $months = self::getMonthsAsInts();
        }
        
        return array_combine(self::getMonthsAsInts(), $months);
    }
}
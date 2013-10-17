<?php

class Hobis_Api_Date_Month_Package
{    
    /**
     * Container for months in long string format
     * 
     * @var array
     */
    protected static $months = array(
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
    
    /**
     * Wrapper method for creating an array of months based on format
     * 
     * @param string
     * @return array
     */
    public static function toArray($format = Hobis_Api_Date_Month::FORMAT_NUMERIC)
    {
        switch ($format) {
        
            case Hobis_Api_Date_Month::FORMAT_STRING_LONG:
                $months = self::$months;
                break;
            
            case Hobis_Api_Date_Month::FORMAT_STRING_SHORT:
                $months = array_map(function ($element) { return substr($element, 0, 3); }, self::$months);
                break;
            
            default:
                $months = range(1, 12);
        }
        
        return array_combine(range(1, 12), $months);
    }
}
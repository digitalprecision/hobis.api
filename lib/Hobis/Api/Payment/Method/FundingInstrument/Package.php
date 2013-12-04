<?php

class Hobis_Api_Payment_Method_FundingInstrument_Package
{
    /**
     * Container for (funding instrument) types
     * 
     * @var array
     */
    protected static $types = array();
    
    /**
     * Wrapper method for getting (funding instrument) type tokens
     * 
     * @return array
     */
    public static function getTypeTokens()
    {
        return array(
            Hobis_Api_Payment_Method_FundingInstrument::TYPE_ID_CARD_VISA       => 'visa',
            Hobis_Api_Payment_Method_FundingInstrument::TYPE_ID_CARD_MASTERCARD => 'mastercard',
        );
    }
    
    /**
     * Wrapper method for getting human readable (funding instrument) types
     *  
     * @return array
     */
    public static function getTypes()
    {
        if (false === Hobis_Api_Array_Package::populated(self::$types)) {
            self::$types = array_map(function($element) { return ucfirst($element); }, self::getTypeTokens());
        }
        
        return self::$types;
    }
}
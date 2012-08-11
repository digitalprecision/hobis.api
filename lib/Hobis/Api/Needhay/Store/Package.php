<?php

class Hobis_Api_Needhay_Store_Package
{
    /**
     * Singletone for storing stores created from factory
     * 
     * @var array
     */
    protected static $stores = array();
    
    /**
     * Factory method for creating store objects
     * 
     * @param array
     * @returns object
     * @throws Hobis_Api_Exception
     */
    public static function factory(array $options)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populated($options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options (%s)', serialize($options)));
        } elseif (!Hobis_Api_Array_Package::populatedKey(Hobis_Api_Needhay_Store::ADAPTER_TYPE, $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[adapterType] (%s)', serialize($options)));
        } elseif (!Hobis_Api_Array_Package::populatedKey(Hobis_Api_Needhay_Store::CONTEXT, $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[context] (%s)', serialize($options)));
        } elseif (!Hobis_Api_Array_Package::populatedKey(Hobis_Api_Needhay_Store::ID, $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[id] (%s)', serialize($options)));
        } elseif (!Hobis_Api_Array_Package::populatedKey(Hobis_Api_Needhay_Store::OBJECT, $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[object] (%s)', serialize($options)));
        }
        //-----
        
        // Localize
        $adapterType    = $options[Hobis_Api_Needhay_Store::ADAPTER_TYPE];
        $context        = $options[Hobis_Api_Needhay_Store::CONTEXT];
        $id             = $options[Hobis_Api_Needhay_Store::ID];
        $object         = $options[Hobis_Api_Needhay_Store::OBJECT];
        
        $hash = Hobis_Api_String_Package::fromArray(
            array(
                $adapterType,
                $context,
                $id,
                $object
            )
        );
        
        $hash = md5($hash);
        
        if (!Hobis_Api_Array_Package::populatedKey($hash, self::$stores)) {
        
            $store = new Hobis_Api_Needhay_Store();

            $store->setAdapterType($adapterType);
            $store->setContext($context);
            $store->setId($id);
            $store->setObject($object);
            
            self::$stores[$hash] = $store;
        }
        
        return self::$stores[$hash];
    }
}

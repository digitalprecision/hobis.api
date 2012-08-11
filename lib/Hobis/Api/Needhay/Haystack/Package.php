<?php

/**
 * Do not confuse haystack with needlestore
 * Needlestore deals only with needle files
 * Haystack deals with assets on the file system accessed by needles
 */
class Hobis_Api_Needhay_Haystack_Package
{
    /**
     * Container of valid haystack operations used for validation
     * 
     * @var array 
     */
    public static $validOps = array(
        Hobis_Api_Needhay_Haystack::OP_CLEAN,
        Hobis_Api_Needhay_Haystack::OP_WRITE
    );
    
    /**
     * Factory method for creating haystack objects
     * 
     * @param object
     * @param object
     * @return object
     * @throws Hobis_Api_Exception
     */
    protected static function execute(array $options)
    {
        //-----
        // Validate
        //-----
        if ((!Hobis_Api_Array_Package::populatedKey('op', $options)) ||
            (!in_array($options['op'], self::$validOps))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[op] (%s)', serialize($options)));
        } 
        
        elseif ((!Hobis_Api_Array_Package::populatedKey('needle', $options)) ||
            (!($options['needle'] instanceof Hobis_Api_Needhay_Needle))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[needle] (%s)', serialize($options)));
        }
        
        elseif ((!Hobis_Api_Array_Package::populatedKey('store', $options)) ||
            (!($options['store'] instanceof Hobis_Api_Needhay_Store))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[store] (%s)', serialize($options)));
        } 
        //-----
        //
        // Localize
        $op     = $options['op'];
        $needle = $options['needle'];
        $store  = $options['store'];        
        
        foreach ($needle->getNeedleCollections() as $needleCollection) {
        
            switch ($needleCollection->getType()) {
                
                case Hobis_Api_Needhay_Needle_Collection::TYPE_AD:
                    $haystack = new Hobis_Api_Needhay_Type_Ad_Haystack();
                    break;
                
                case Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION:
                    $haystack = new Hobis_Api_Needhay_Type_Description_Haystack();
                    break;
                
                case Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE:
                    $haystack = new Hobis_Api_Needhay_Type_Image_Haystack();
                    break;
                    
                default:
                    throw new Hobis_Api_Exception(sprintf('Invalid $needle->getType() (%s)', $needleCollection->getType()));
            }
            
            $haystack->setNeedle($needle);
            $haystack->setStore($store);
        
            switch ($op) {
                
                case Hobis_Api_Needhay_Haystack::OP_CLEAN:
                    $haystack->clean();
                    break;
                    
                case Hobis_Api_Needhay_Haystack::OP_WRITE:
                    $haystack->write();
                    break;
                    
                default:
                    throw new Hobis_Api_Exception(sprintf('Invalid $op (%s)', $op));
            }
        }
    }
    
    /**
     * Wrapper method for calling execute, with clean param
     * 
     * @param object
     * @param object
     */
    public static function clean(Hobis_Api_Needhay_Needle $needle, Hobis_Api_Needhay_Store $store)
    {
        self::execute(
            array(
                'needle'    => $needle,
                'store'     => $store,
                'op'        => Hobis_Api_Needhay_Haystack::OP_CLEAN
            )
        );
    }           
    
    /**
     * Wrapper method for calling execute, with write param
     * 
     * @param object
     * @param object
     */
    public static function write(Hobis_Api_Needhay_Needle $needle, Hobis_Api_Needhay_Store $store)
    {
        self::execute(
            array(
                'needle'    => $needle,
                'store'     => $store,
                'op'        => Hobis_Api_Needhay_Haystack::OP_WRITE
            )
        );
    }
}

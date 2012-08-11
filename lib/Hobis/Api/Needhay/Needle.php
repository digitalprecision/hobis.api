<?php

/**
 * A needle contains all the data needed to locate assets located throughout the haystack (filesystem)
 *  This is an object representation of data containted within a needle file
 *  Needle data may contain mutiple 'types' of needle collections
 */  
class Hobis_Api_NeedHay_Needle
{
    /**
     * Container for needle objects
     * 
     * @var array
     */
    protected $needleCollections = array();
    
    /**
     * Add needle collection to current stack
     * 
     * @param object
     */
    public function setNeedleCollection(Hobis_Api_NeedHay_Needle_Collection $needleCollection)
    {
        $this->needleCollections[$needleCollection->getType()] = $needleCollection;
    }
    
    /**
     * Get needle collections in current stack
     * 
     * @return array
     */
    public function getNeedleCollections()
    {
        // Keep needle collections in order
        ksort($this->needleCollections);

        return $this->needleCollections;
    }
    
    /**
     * Get specific needle collection from current stack
     * 
     * @param string
     * @return object
     */
    public function getNeedleCollection($needleCollectionType)
    {
        if (Hobis_Api_Array_Package::populatedKey($needleCollectionType, $this->needleCollections)) {
            return $this->needleCollections[$needleCollectionType];
        }
        
        // If no collection was found create new object to support fluid interface
        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();
        
        $needleCollection->setType($needleCollectionType);
        
        return $needleCollection;
    }
    
    /**
     * Remove a needleCollection from current stack
     * 
     * @param object
     * @throws Hobis_Api_Exception
     */
    public function removeNeedleCollection(Hobis_Api_NeedHay_Needle_Collection $needleCollection)
    {
        if (!Hobis_Api_Array_Package::populatedKey($needleCollection->getType(), $this->needleCollections)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $this->needleCollections[$type] (%s)', $needleCollection->getType()));
        }

        unset($this->needleCollections[$needleCollection->getType()]);        
    }
    
    /**
     * Replace an existing needle collection in current stack
     * 
     * @param object
     * 
     */
    public function replaceNeedleCollection(Hobis_Api_NeedHay_Needle_Collection $needleCollection)
    {
        // Removing needle shouldn't break calling code
        try {   
            $this->removeNeedleCollection($needleCollection);
        } catch (Exception $e) {
            
            Hobis_Api_Log_Package::toErrorLog()->debug($e);
            
            $e = null;
        }
        
        $this->setNeedleCollection($needleCollection);        
    }
}
<?php
/**
 * This class relates to a single needle collection type
 *  When a needle is read, contents are stored in collections on two levels;
 *      "Needle Collection" (this) and a "Pointer collection"
 */
class Hobis_Api_NeedHay_Needle_Collection
{
    /**
     * Collection types
     */
    const TYPE_AD           = 'ad';
    const TYPE_DESCRIPTION  = 'description';
    const TYPE_IMAGE        = 'image';
    
    /**
     * Container for pointer collection objects
     * 
     * @var array
     */
    protected $pointerCollections = array();
    
    /**
     * Container for needle collection type
     * 
     * @var string
     */
    protected $type;    

    /**
     * Add a pointer collection to current stack
     * 
     * @param object
     */
    public function setPointerCollection(Hobis_Api_Needhay_Pointer_Collection $pointerCollection)
    {
        $this->pointerCollections[$pointerCollection->getId()] = $pointerCollection;
    }

    /**
     * Setter for type
     * 
     * @param string 
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * Method for returning all pointer collections in current stack
     * 
     * @return array
     */
    public function getPointerCollections()
    {
        return $this->pointerCollections;
    }
    
    /**
     * Method for returning a specific pointer collection in current stack
     * 
     * @param int $id
     * @return object 
     */
    public function getPointerCollection($id)
    {
        // Instantiates a new object upon false eval to support fluid interface
        return (Hobis_Api_Array_Package::populatedKey($id, $this->pointerCollections)) ? $this->pointerCollections[$id] : new Hobis_Api_Needhay_Pointer_Collection();
    }

    /**
     * Getter for type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
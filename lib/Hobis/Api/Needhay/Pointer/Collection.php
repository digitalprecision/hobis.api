<?php

/**
 * This class relates to a single type of pointer collection
 *  When a needle is read, contents are stored in collections on two levels;
 *      needle and a pointer (this) collection 
 */

class Hobis_Api_NeedHay_Pointer_Collection
{
    /**
     * Valid mode types 
     */
    const MODE_REMOVE   = 'remove';
    const MODE_ADD      = 'add';
    const MODE_READ     = 'read';

    /**
     * Id of current pointer collection
     * 
     * @var int
     */
    protected $id;
    
    /**
     * Mode of current pointer collection 
     * 
     * @var string
     */
    protected $mode;
    
    /**
     * Container of pointer objects relating to current pointer collection
     * 
     * @var array
     */
    protected $pointers = array();

    /**
     * Setter for id
     * 
     * @param int
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Setter for mod
     * 
     * @param string
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Add pointer to current pointers stack
     * 
     * @param object
     */
    public function setPointer(Hobis_Api_Needhay_Pointer $pointer)
    {
        $this->pointers[] = $pointer;
    }

    /**
     * Getter for id 
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Getter for mode
     * 
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
    
    /**
     * Return a specific pointer in current stack
     * 
     * @param int
     * @return object
     */
    public function getPointer($id)
    {
        // New object is created on false eval to support fluid interface
        return (Hobis_Api_Array_Package::populatedKey($id, $this->pointers)) ? $this->pointers[$id] : new Hobis_Api_Needhay_Pointer();
    }

    /**
     * Return all pointers in current stack
     * 
     * @return array
     */
    public function getPointers()
    {
        return $this->pointers;
    }
}

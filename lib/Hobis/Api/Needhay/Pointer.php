<?php

/**
 * A pointer is an object representation of needle contents
 *  Pointers point directly to haystack assets
 */
class Hobis_Api_NeedHay_Pointer
{
    /**
     * Asset content can take on various contexts depending upon the pointer
     *  being used
     *  A description pointer will use asset content to store the contents of
     *      the actual file
     *  An image pointer will store image uri
     * 
     * @var string
     */
    protected $assetContent;
    
    /**
     * Asset name refers to how the file will be stored within haystack (filename)
     * 
     * @var string
     */    
    protected $assetName;
    
    /**
     * Setter for asset content
     * 
     * @param string
     */
    public function setAssetContent($assetContent)
    {
        $this->assetContent = $assetContent;
    }

    /**
     * Setter for asset name
     * 
     * @param string
     */
    public function setAssetName($assetName)
    {
        $this->assetName = $assetName;
    }   

    /**
     * Getter for asset content
     * 
     * @return string
     */
    public function getAssetContent()
    {
        return $this->assetContent;
    }
    
    /**
     * Getter for asset name
     * 
     * @return type 
     */
    public function getAssetName()
    {
        return $this->assetName;
    }  
    
    /**
     * Wrapper method for generating haystack uri
     *  Each pointer should know how to construct it's own uri
     *  Passing store in for the ability to generate higher level dir struct
     *
     * @param object
     * @return string
     */
    public function getHaystackUri(Hobis_Api_Needhay_Store $store)
    {
        $haystackUri = Hobis_Api_Directory_Package::fromArray(
            array(
                Hobis_Api_Needhay_Package::generatePath(array('store' => $store, 'type' => Hobis_Api_Needhay::HAYSTACK)),
                $this->getAssetName()
            )
        );
        
        return (Hobis_Api_File_Package::isFile($haystackUri)) ? $haystackUri : null;
    }
}
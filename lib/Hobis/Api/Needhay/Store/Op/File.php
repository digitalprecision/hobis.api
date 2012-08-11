<?php

abstract class Hobis_Api_Needhay_Store_Op_File extends Hobis_Api_Needhay_Store_Op
{
    /**
     * Uri to needle file
     * 
     * @var string 
     */
    protected $needleFileUri;
    
    /**
     * Setter for needle file uri
     * 
     * @param string
     */
    public function setNeedleFileUri($needleFileUri)
    {
        $this->needleFileUri = $needleFileUri;
    }
    
    /**
     * Getter for needle file uri
     * 
     * @return string
     */
    public function getNeedleFileUri()
    {
        return $this->needleFileUri;
    }
}
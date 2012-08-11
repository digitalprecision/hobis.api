<?php

/**
 * Pointer data related to images goes here
 */
class Hobis_Api_Needhay_Type_Image_Pointer extends Hobis_Api_Needhay_Pointer
{
    /**
     * Size code allows images to be stored according to size code;
     *  original, large, med, small
     *  This allows calling code to dictate how big of an image is necessary
     * 
     * @var string
     */
    protected $sizeCode;

    /**
     * Setter for size code
     * 
     * @param string
     */
    public function setSizeCode($sizeCode)
    {
        $this->sizeCode = $sizeCode;
    }    

    /**
     * Getter for size code
     * 
     * @return string
     */
    public function getSizeCode()
    {
        return $this->sizeCode;
    }
}
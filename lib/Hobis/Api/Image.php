<?php

class Hobis_Api_Image
{
    //-----
    // Size codes
    //-----
    const SIZE_CODE_LARGE       = 'lg';
    const SIZE_CODE_MEDIUM      = 'me';
    const SIZE_CODE_ORIGINAL    = 'og';
    const SIZE_CODE_SMALL       = 'sm';
    //-----

    //-----
    // Sizes (in pixels)
    //-----
    const SMALL_WIDTH	= 120;
    const SMALL_HEIGHT	= 120;

    const MED_WIDTH     = 400;
    const MED_HEIGHT	= 400;

    const LARGE_WIDTH	= 720;
    const LARGE_HEIGHT	= 720;
    //-----

    const HEIGHT	= 'height';
    const WIDTH		= 'width';

    /**
     * Container for height
     *
     * @var int
     */
    protected $height;

    /**
     * Container for sizecode (i.e. og (original), lg (large) etc)
     *
     * @var string
     */
    protected $sizeCode;

    /**
     * Container for uri
     *
     * @var string
     */
    protected $uri;

    /**
     * Container for width
     *
     * @var int
     */
    protected $width;

    /**
     * Setter for height
     *
     * @param int
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Setter for sizecode
     *
     * @param string
     */
    public function setSizeCode($sizeCode)
    {
        $this->sizeCode = $sizeCode;
    }

    /**
     * Setter for uri
     *
     * @param string
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Setter for width
     *
     * @param int
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Getter for height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Getter for sizecode
     *
     * @return string
     */
    public function getSizeCode()
    {
        return $this->sizeCode;
    }

    /**
     * Getter for uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Getter for width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
}
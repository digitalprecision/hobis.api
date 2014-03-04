<?php

class Hobis_Api_Navigation extends Zend_Navigation
{
    const LABEL = 'label';
    const URI   = 'uri';
	
	const CONTAINER_TYPE_FLUID  = 'fluid';
    const CONTAINER_TYPE_STATIC = 'static';
			
    /**
     * Container for brand settings
     *  Brand refers to primary site attributes like name and homepage uri
     * 
     * @var array
     */
    protected $brandSettings;
    
    /**
     * Container for container type
     *  i.e. static, fluid
     *  This dictates how the navbar will be rendered across various screens
     * 
     * @var string 
     */
	protected $containerType;
	
    /**
     * Setter for brand
     * 
     * @param array
     */
    public function setBrandSettings(array $brandSettings)
    {
        $this->brandSettings = $brandSettings;
    }
    
    /**
     * Setter for container type
     * 
     * @param array
     */
	public function setContainerType($containerType)
	{
		$this->containerType = $containerType;
	}
    
    /**
     * Getter for brand
     * 
     * @return array
     */
    public function getBrandSettings()
    {
        return $this->brandSettings;
    }
	
    /**
     * Getter for container type
     * 
     * @return string
     */
	public function getContainerType()
	{
		return $this->containerType;
	}
}

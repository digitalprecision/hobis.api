<?php

abstract class Hobis_Api_Payment_Gateway_Adapter
{   
    const ID_TYPE_PAYPAL = 1;
    
    const ID_MODE_DEV   = 1;
    const ID_MODE_PROD  = 2;
    
    /**
     * Container for api key
     *  This should be the full api key build, as we do not assume api key constructs
     *      dictated by adapters
     * 
     * @var string
     */
    protected $apiKey;
    
    /**
     * Container for storing established connections, this allows us to reuse 
     * 
     * @var array
     */
    protected static $connection = array();
    
    /**
     * Container for mode id
     *  Allows for switching between dev, prod, etc
     * 
     * @var string
     */
    protected $modeId;
    
    /**
     * Every child class must have ability to return a connection object
     */
    abstract protected function getConnection();
    
    /**
     * Every child class must have ability to create vault item
     */
    abstract public function createVaultItem(Hobis_Api_Payment_Method $paymentMethod);
    
    /**
     * Setter for api key
     * 
     * @param string
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Setter for modeId
     * 
     * @param int
     */
    public function setModeId($modeId)
    {
        $this->modeId = $modeId;
    }
    
    /**
     * Getter for api key
     * 
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
    
    /**
     * Getter for modeId
     * 
     * @return string
     */
    public function getModeId()
    {
        return $this->modeId;
    }
    
    /**
     * Wrapper method for generating a container key
     *  Useful for distinguishing between singleton items within a single array
     * 
     * @param string
     * @return string
     */
    protected function generateContainerKey($input)
    {
        return md5($input);
    }
}
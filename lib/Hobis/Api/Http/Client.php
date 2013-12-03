<?php

class Hobis_Api_Http_Client extends Zend_Http_Client
{
    /**
     * Container for access token
     *  This will help in reducing number of requests to vendor, set once use many
     * 
     * @var string
     */
    protected $accessToken;
    
    /**
     * Setter for access token
     * 
     * @param string
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
    
    /**
     * Getter for access token
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
    
    /**
     * Wrapper method for sanitizing connection object
     */
    public function sanitize()
    {
        // Sanitize all params, otherwise we may run into unintended behaviors due to previous set params
        $this->resetParameters(true);
    }
}

<?php

class Hobis_Api_Payment_Gateway_Adapter_Paypal extends Hobis_Api_Payment_Gateway_Adapter
{   
    /**
     * Wrapper method for returning payment uri
     * 
     * @return string
     */
    protected function getUriPayment()
    {
        switch ($this->getMode()) {
            
            case parent::MODE_PROD:
                return 'https://api.sandbox.paypal.com/v1/payments/payment';
                
            default:
                return 'https://api.sandbox.paypal.com/v1/payments/payment';
        }
    }
    
    /**
     * Wrapper method for returning token uri
     * 
     * @return string
     */
    protected function getUriToken()
    {
        switch ($this->getMode()) {
            
            case parent::MODE_PROD:
                return 'https://api.sandbox.paypal.com/v1/oauth2/token';
                
            default:
                return 'https://api.sandbox.paypal.com/v1/oauth2/token';
        }
    }
    
    /**
     * Wrapper method for returning vault uri
     * 
     * @return string
     */
    protected function getUriVault()
    {
        switch ($this->getMode()) {
            
            case parent::MODE_PROD:
                return 'https://api.sandbox.paypal.com/v1/vault/credit-card';
                
            default:
                return 'https://api.sandbox.paypal.com/v1/vault/credit-card';
        }
    }
    
    /**
     * Wrapper method for creating a vault item
     *  We will hydrate the passed in object with response data
     * 
     * @param object
     * @return object
     */
    public function createVaultItem(Hobis_Api_Payment_Gateway_Vault_Item $item)
    {
        $this->getConnection()->sanitize();
        
        // Authorization:Bearer is not a standard http header, so we have to disable strict header checking
        $this->getConnection()->setConfig(array('strict' => false));

        $this->getConnection()->setUri($this->getUriVault());
                
        $payload = array(
            'expire_month'  => $item->expireMonth,
            'expire_year'   => $item->expireYear,
            'first_name'    => $item->nameFirst,
            'last_name'     => $item->nameLast,
            'number'        => $item->number,
            'payer_id'      => $item->userId,
            'type'          => $item->type
        );
        
        $this->getConnection()->setRawData(json_encode($payload), Hobis_Api_Http_Client::ENC_URLENCODED);
                
        $this->getConnection()->setHeaders(
            array(
                'Accept'            => 'application/json',
                'Accept-Language'   => 'en_US',
                'Content-Type'      => 'application/json',
                'Authorization'     => sprintf('Bearer %s', $this->getConnection()->getAccessToken())
            )
        );

        $response = $this->getConnection()->request(Hobis_Api_Http_Client::POST);
        
        var_dump($response);
    }
    
    /**
     * Getter for connection object
     *  Note: It is a bit confusing, but there are two layers of connections and adapters
     *  First we have the Gateway and Gateway Adapter layers, these are the layers used by calling code
     *  Then, we have Client and Client Adapter layers, these are the layers that issue the actual queries
     *      against remote web services
     * 
     * @return object
     */
    protected function getConnection()
    {
        $containerKey = $this->generateContainerKey(get_class($this));
        
        if (false === Hobis_Api_Array_Package::populatedKey($containerKey, parent::$connection)) {
            
            //-----
            // Init connection
            //-----
            $connection = new Hobis_Api_Http_Client;
            
            $connection->setAdapter(new Hobis_Api_Http_Client_Adapter_Curl);
            //-----
            
            //-----
            // Lets set access token, so we can take advantage of the singleton object, and reduce requests
            //  to vendor. Tried to do this as a sep method, but ran into chicken/egg scenario
            //-----
            $connection->getAdapter()->setConfig(array('curloptions' => array(CURLOPT_USERPWD => $this->apiKey)));

            $connection->setUri($this->getUriToken());

            $connection->setParameterPost('grant_type', 'client_credentials');
            
            $connection->setHeaders(
                array(
                    'Accept'            => 'application/json',
                    'Accept-Language'   => 'en_US',
                    'Content-Type'      => 'application/json'
                )
            );

            $response = $connection->request(Hobis_Api_Http_Client::POST);

            $body = json_decode($response->getBody());

            if (false === isset($body->access_token)) {
                throw new Hobis_Api_Exception(sprintf('Invalid access_token: %s', serialize($body)));
            }
            
            $connection->setAccessToken($body->access_token);
            //-----
        
            parent::$connection[$containerKey] = $connection;
        }
        
        return parent::$connection[$containerKey];
    }
}
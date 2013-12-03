<?php

class Hobis_Api_Payment_Gateway
{
    /**
     * Container for payment gateway adapter
     *  This allows us to use various adapters to communicate to various payment gateway
     *  providers
     * 
     * @var object
     */
    protected $adapter;
    
    /**
     * Setter for adapter
     * 
     * @param object
     */
    public function setAdapter(Hobis_Api_Payment_Gateway_Adapter $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * Getter for adapter
     * 
     * @return object
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
    
    /**
     * Wrapper method for creating a vault item
     *  A vault item is a payment method stored with payment gateway provider, and is usually
     *      associated by a unique id returned when successfully stored
     * 
     * @param object
     */
    public function createVaultItem(Hobis_Api_Payment_Gateway_Vault_Item $item)
    {
        $this->getAdapter()->createVaultItem($item);
    }
}
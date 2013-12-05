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
     * 
     * @param object
     * @return object
     */
    public function createVaultItem(Hobis_Api_Payment_Method_Transport $paymentMethodTransport)
    {
        return $this->getAdapter()->createVaultItem($paymentMethodTransport);
    }
    
    /**
     * Wrapper method for deleting a vault item
     * 
     * @param object
     * @return object
     */
    public function deleteVaultItem(Hobis_Api_Payment_Method_Transport $paymentMethodTransport)
    {
        return $this->getAdapter()->deleteVaultItem($paymentMethodTransport);
    }
}
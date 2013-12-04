<?php

class Hobis_Api_Payment_Method
{
    const ID_STATUS_ACTIVE      = 1;
    const ID_STATUS_INACTIVE    = 2;
    
    /**
     * Container for funding instrument object
     *  Funding instrument represents data used in storing payment method information with vendor via payment gateway
     * 
     * @var object
     */
    protected $fundingInstrument;
    
    /**
     * Container for userId
     *  
     * @var string (abstract by design)
     */
    protected $userId;
    
    /**
     * Container for vault item object
     *  Vault item represents data used in referencing payment method information to vendor via payment gateway
     * 
     * @var object
     */
    protected $vaultItem;
    
    /**
     * Setter for funding instrument
     * 
     * @param object
     */
    public function setFundingInstrument(Hobis_Api_Payment_Method_FundingInstrument $fundingInstrument)
    {
        $this->fundingInstrument = $fundingInstrument;
    }
    
    /**
     * Setter for user id
     * 
     * @param string
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    
    /**
     * Setter for vault item
     * 
     * @param object
     */
    public function setVaultItem(Hobis_Api_Payment_Method_VaultItem $vaultItem)
    {
        $this->vaultItem = $vaultItem;
    }
    
    /**
     * Getter for funding instrument
     * 
     * @return object
     */
    public function getFundingInstrument()
    {
        return $this->fundingInstrument;
    }
    
    /**
     * Getter for user id
     * 
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * Setter for vault item
     * 
     * @return object
     */
    public function getVaultItem()
    {
        return $this->vaultItem;
    }
}
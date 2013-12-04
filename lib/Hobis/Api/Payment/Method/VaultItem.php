<?php

class Hobis_Api_Payment_Method_VaultItem
{
    /**
     * Container for createdAt timestamp
     *  Time vault item was created
     * 
     * @var int
     */
    protected $createdAt;
    
    /**
     * Container for deleted timestamp
     *  Time vault item was deleted from vault
     * 
     * @var int
     */
    protected $deletedAt;
    
    /**
     * Container for expiredAt timestamp
     *  Time vault item expires from vault
     * 
     * @var int
     */
    protected $expiredAt;
    
    /**
     * Container for id
     *  The id returned from vault used to look up this vault item in the future
     * 
     * @var string
     */
    protected $id;
    
    /**
     * Container for mask
     *  If the funding instrument being stored in the vault is a CC, this value is the mask returned by the vault
     * 
     * @var string
     */
    protected $mask;
    
    /**
     * Container for updatedAt
     *  Time vault item was updated
     * 
     * @var int
     */
    protected $updatedAt;
    
    /**
     * Setter for created at
     * 
     * @param int
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    /**
     * Setter for deleted at
     * 
     * @param int
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }
    
    /**
     * Setter for expired at
     * 
     * @param int
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;
    }
    
    /**
     * Setter for id
     * 
     * @param string
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Setter for mask
     * 
     * @param string
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }
    
    /**
     * Setter for update at
     * 
     * @param int
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
    
    /**
     * Getter for created at
     * 
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * Getter for deleted at
     * 
     * @return int
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
    
    /**
     * Getter for expired at
     * 
     * @return int
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }
    
    /**
     * Getter for id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Getter for mask
     * 
     * @return string
     */
    public function getMask()
    {
        return $this->mask;
    }
    
    /**
     * Getter for update at
     * 
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
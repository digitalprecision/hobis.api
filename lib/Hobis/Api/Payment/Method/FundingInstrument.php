<?php

class Hobis_Api_Payment_Method_FundingInstrument
{
    const ID_TYPE_VISA          = 1;
    const ID_TYPE_MASTERCARD    = 2;
    
    /**
     * Container for expire month
     * 
     * @var int
     */
    protected $expireMonth;
    
    /**
     * Container for expire year
     * 
     * @var int
     */
    protected $expireYear;
    
    /**
     * Container for first name
     * 
     * @var string
     */
    protected $nameFirst;
    
    /**
     * Container for last name
     * 
     * @var string
     */
    protected $nameLast;
    
    /**
     * Container for number
     *  Cast as string for flexibility
     * 
     * @var string
     */
    protected $number;
    
    /**
     * Container for type id
     * 
     * @var int
     */
    protected $typeId;
    
    /**
     * Setter for expire month
     * 
     * @param int
     */
    public function setExpireMonth($expireMonth)
    {
        $this->expireMonth = $expireMonth;
    }
    
    /**
     * Setter for expire year
     * 
     * @param int
     */
    public function setExpireYear($expireYear)
    {
        $this->expireYear = $expireYear;
    }
    
    /**
     * Setter for first name
     * 
     * @param string
     */
    public function setNameFirst($nameFirst)
    {
        $this->nameFirst = $nameFirst;
    }
    
    /**
     * Setter for last name
     * 
     * @param string
     */
    public function setNameLast($nameLast)
    {
        $this->nameLast = $nameLast;
    }
    
    /**
     * Setter for number
     * 
     * @param string
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }
    
    /**
     * Setter for typeId
     * 
     * @param int
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }
    
    /**
     * Getter for expire month
     * 
     * @return int
     */
    public function getExpireMonth()
    {
        return $this->expireMonth;
    }
    
    /**
     * Getter for expire year
     * 
     * @return int
     */
    public function getExpireYear()
    {
        return $this->expireYear;
    }
    
    /**
     * Getter for first name
     * 
     * @return string
     */
    public function getNameFirst()
    {
        return $this->nameFirst;
    }
    
    /**
     * Getter for last name
     * 
     * @return string
     */
    public function getNameLast()
    {
        return $this->nameLast;
    }
    
    /**
     * Getter for number
     * 
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
    
    /**
     * Getter for typeId
     * 
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }
    
    /**
     * Wrapper method for returning a type token
     *  Hopefully these don't change per adapter, but if they do will have to move to adapter level
     * 
     * @param int
     * @throws Hobis_Api_Exception
     */
    public function getTypeToken()
    {
        switch ($this->getTypeId()) {
            
            case self::ID_TYPE_VISA:
                return 'visa';
                
            case self::ID_TYPE_MASTERCARD:
                return 'mastercard';
                
            default:
                throw new Hobis_Api_Exception(sprintf('Invalid $typeId: %s', serialize($typeId)));
        }
    }
}
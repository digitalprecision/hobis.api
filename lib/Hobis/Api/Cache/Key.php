<?php

class Hobis_Api_Cache_Key
{
    const SEPARATOR = '_';

    const STATUS_HIT    = 'hit';
    const STATUS_MISS   = 'miss';

    const LOG_NAME_STATUS   = 'cacheKeyStatus';
    const LOG_URI_STATUS    = '/var/log/cache/keyStatus.log';
	
	const EXPIRY_ID_30_DAY		= 1;
	const EXPIRY_ID_3_HOUR		= 2;
	const EXPIRY_ID_30_MINUTE	= 3;
	const EXPIRY_ID_5_MINUTE	= 4;
	const EXPIRY_ID_30_SECOND	= 5;

    /**
     * TTL for a key
     *
     * @var int
     */
    protected $expiry;

    /**
     * How the value will be accessed
     *
     * @var string
     */
    protected $key;

    /**
     * Value related to a specific key
     *
     * @var string
     */
    protected $value;

    /**
     * Setter for expiry
     *
     * @param int
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
    }
	
	/**
	 * Wrapper method for setting expiry by pre-defined id
	 * 	Helpful so we can centralize common computations for specified durations
	 */
	public function setExpiryById($token)
	{
		switch ($token) {
			
			case self::EXPIRY_ID_30_SECOND:
				
				$expiry = 30;
				break;
			
			case self::EXPIRY_ID_5_MINUTE:
				
				$expiry = (60*5);
				break;
			
			case self::EXPIRY_ID_3_HOUR:
				
				$expiry = (60*60*3);
				break;
			
			case self::EXPIRY_ID_30_DAY:
				
				$expiry = (60*60*24*30);
				break;
				
			default:
				
				$expiry = 3600;
		}
		
		$this->expiry = $expiry;
	}

    /**
     * Setter for key
     *
     * @param string
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Setter for value
     *
     * @param string
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
	
	/**
	 * Wrapper getter (EXPLICIT) to lessen confusion in calling code
	 * 
	 * @return string
	 */
	public function toString()
	{
		return $this->getKey();
	}

    /**
     * Getter for expiry
     *
     * @return int
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Getter for key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Getter for value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
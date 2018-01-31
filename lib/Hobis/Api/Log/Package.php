<?php

class Hobis_Api_Log_Package
{
	/**
	 * Logger singleton
	 *
	 * @var array (of objects)
	 */
	protected static $logger = array();

	/**
     * Getter for logger singleton
     *
     * @return object
     */
    public static function getLogger($logName = Hobis_Api_Log::NAME_STD_OUT)
    {
    	if (!Hobis_Api_Array_Package::populatedKey($logName, self::$logger)) {
			throw new Hobis_Api_Exception('Invalid self::$logger[$logName], be sure you register logger with same name first');
		} elseif (!(self::$logger[$logName] instanceof Hobis_Api_Log)) {
			throw new Hobis_Api_Exception('self::$logger[$logName] exists, but is not a valid log object');
		}

		return self::$logger[$logName];
    }

    /**
     * Factory for logger singleton
	 *
	 * @param string $logPath
	 * @return object
     */
    public static function factory($logName = Hobis_Api_Log::NAME_STD_OUT, $logPath = Hobis_Api_Log::URI_STD_OUT)
    {
        // If flagged as error log, we cannot use stream, instead just set to null so we still have access to accessors
        if (Hobis_Api_Log::NAME_PHP_ERROR === $logName) {
            $writer = new Hobis_Api_Log_Writer_Error;
        } else {
            $writer = new Zend_Log_Writer_Stream($logPath);
        }

	    $formatter = new Zend_Log_Formatter_Simple(Hobis_Api_Log::FORMAT . PHP_EOL);

	    $writer->setFormatter($formatter);

	    return new Hobis_Api_Log($writer);
    }

    /**
     * Wrapper method for preparing message for logging
     *
     * @param string
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function prepMessage($message)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($message)) {

            throw new Hobis_Api_Exception('Invalid $message');
        }

        if (Hobis_Api_Array_Package::populatedKey('X_REQUEST_ID', $_SERVER)) {

            $message = $_SERVER['X_REQUEST_ID'] . ': ' . $message;
        }

        return $message;
    }

	/**
	 * Wrapper method for registering a logger
	 *
	 * @param string $logName
	 * @param string $logPath
	 */
	public static function registerLogger($logName = Hobis_Api_Log::NAME_STD_OUT, $logUri = Hobis_Api_Log::URI_STD_OUT)
	{
		if (false === Hobis_Api_Array_Package::populatedKey($logName, self::$logger)) {
			self::$logger[$logName] = self::factory($logName, $logUri);
		}
	}

	/**
	 * Convienence method for logging to php error log
	 */
	public static function toErrorLog()
	{
		self::registerLogger(Hobis_Api_Log::NAME_PHP_ERROR, Hobis_Api_Log::URI_PHP_ERROR);
        
		return self::getLogger(Hobis_Api_Log::NAME_PHP_ERROR);
	}
}

<?php

/**
 * Wrapper api for Zend_Log
 *
 */
class Hobis_Api_Log extends Zend_Log
{
    // DO NOT ADD any type of method/line debugging flags here
    //  They will be provided by Hobis_Api_Exception
    //  This is meant to be a generic logger, not an exception logger
    const FORMAT = '%timestamp% %hostname% %priorityName%(%priority%): %message%';

    // Arbitrary names which will link to corresponding paths
    const NAME_PHP_ERROR    = 'errorLog';
    const NAME_STD_OUT      = 'stdOut';

    // Paths to commonly used log files
    const URI_PHP_ERROR    = '/var/log/php/oops.log';
    const URI_STD_OUT      = 'php://output';

    // When we need to display debug messages in a production env
    const DEBUG_ON_PROD_OVERRIDE = 'debug_on_prod_override';

    /**
     * Class constructor.  Create a new logger
     *
     * @param Zend_Log_Writer_Abstract|null  $writer  default writer
     */
    public function __construct(Zend_Log_Writer_Abstract $writer = null)
    {
        parent::__construct($writer);

        $this->setEventItem('hostname', php_uname('n'));
        $this->setEventItem('timestamp', date('Y-m-d@H:i:s'));
    }

	/**
	 * Overriding call so we can handle exceptions
	 *
	 * @param string $method
	 * @param array $params
	 * @throws Exception
	 */
	public function __call($method, $params)
	{
        // Reduce chatter on production envs
        //  Only display debug messages if override told us to
        if (($method === 'debug') &&
            (Hobis_Api_Environment_Package::getValue(Hobis_Api_Environment::VAR_LABEL_SERVICE) === Hobis_Api_Environment::PROD) &&
            (!in_array(self::DEBUG_ON_PROD_OVERRIDE, $params))) {
            return;
        }

        foreach ($params as $param) {

            if (self::DEBUG_ON_PROD_OVERRIDE === $param) {
                continue;
            }

            elseif ($param instanceof Hobis_Api_Exception) {

                $message = (string) $param;

                // Leaving this here for convenience
                //  Try not to leave it on for two long as it adds considerable lines to the log
                //$message = $param->getTraceAsString();
            }

            else {
                $message = $param;
            }

            parent::__call($method, array($message));
        }
	}

    /**
     * Overriding parent so we can prep message
     */
    public function log($message, $priority, $extras = null)
    {
        $message = Hobis_Api_Log_Package::prepMessage($message);

        parent::log($message, $priority, $extras);
    }
}
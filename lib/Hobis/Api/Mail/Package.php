<?php

class Hobis_Api_Mail_Package
{
    /**
     * Container for config
     *
     * @var array
     */
    protected static $config;

	/**
	 * Factory for creating a mail object based off smtp settings in config file
	 *
	 * @param array $options
	 * @return object Zend_Mail
	 * @throws Exception
	 */
	public static function factory(array $options)
	{
        // Validate
        if (false === Hobis_Api_Array_Package::populatedKey('contextId', $options)) {
            
            throw new Exception(sprintf('Invalid $options[contextId]: %s', serialize($options)));
        }

        if (false === isset(self::$config)) {

            $config = sfYaml::load(self::getConfig());

            //-----
            // Validate config
            //-----
            if (false ===  Hobis_Api_Array_Package::populatedKey('contexts', $config)) {
                throw new Hobis_Api_Exception(sprintf('Invalid contexts: %s', serialize($config)));
            }
            
            elseif (false ===  Hobis_Api_Array_Package::populatedKey('port', $config)) {
                throw new Hobis_Api_Exception(sprintf('Invalid port: %s', serialize($config)));
            }
            //-----
            
            self::$config = $config;
        }

        if (false === Hobis_Api_Array_Package::populatedKey($options['contextId'], self::$config['contexts'])) {

            throw new Exception(sprintf('ContextId mismatch: %s', serialize($options)));
        }

        //-----
        // smtp conf
        //  Some fields may be optional depending upon setup
        //-----        
        $smtpConf['port'] = self::$config['port'];
        
        if ((true === Hobis_Api_Array_Package::populatedKey('login_email_address', self::$config)) ||
            (true === Hobis_Api_Array_Package::populatedKey('login_password', self::$config))) {
        
            if (false === Hobis_Api_Array_Package::populatedKey('login_email_address', self::$config)) {
                
                throw new Exception(sprintf('login_password given but no login_email_address: %s', serialize(self::$config)));
            }
            
            elseif (false === Hobis_Api_Array_Package::populatedKey('login_password', self::$config)) {
                
                throw new Exception(sprintf('login_email_address given but no login_password: %s', serialize(self::$config)));
            }
            
            elseif (587 !== $smtpConf['port']) {
                
                throw new Exception(sprintf('Invalid port, expected secure port (587): %s', serialize(self::$config)));
            }
            
            $smtpConf['auth']       = 'login';
            $smtpConf['password']   = self::$config['login_password'];
            $smtpConf['ssl']        = 'tls';
            $smtpConf['username']   = self::$config['login_email_address'];
        }
        //-----
        
        //-----
        // Transport host
        //-----
        $transportHost = self::$config['contexts'][$options['contextId']];
        
        if (true === Hobis_Api_Array_Package::populatedKey('domain', self::$config)) {
        
            $transportHost .= sprintf('.%s', self::$config['domain']);
        }
        //-----

        $transport = new Zend_Mail_Transport_Smtp($transportHost, $smtpConf);

        // Hobis_Api_Mail extends Zend_Mail so setting this singleton via Zend_Mail
        // (Hobis_Api_Mail parent) will be accessible by Hobis_Api_Mail
        // This singleton is important so send() calls do not need to specify a transport
        // In short, zends design is flawed, as they should have used a setter/getter which accessed the singleton
        // i.e. $mail->setTransport()
        Zend_Mail::setDefaultTransport($transport);

        $mail = new Hobis_Api_Mail('UTF-8');

        return $mail;
    }

    /**
     * Wrapper method for cleaning specified parts of email content
     *
     * @param string $type
     * @param string $taintedData
     */
    public static function cleanEmailContent($type, $taintedData)
    {
        switch ($type) {

            case Hobis_Api_Mail::RESPONSE_RECIPIENT:
                $taintedData = preg_replace("/[\"<>]/", "", $taintedData);
                $taintedData = trim(str_ireplace(Hobis_Api_Mail::RESPONSE_RECIPIENT . " ", "", $taintedData));

                if (stripos($taintedData, 'rfc822;') !== false) {
                    $taintedData = trim(str_ireplace("rfc822;", "", $taintedData));
                }

                break;

            case Hobis_Api_Mail::RESPONSE_STATUS:
                $taintedData = trim(str_ireplace(Hobis_Api_Mail::RESPONSE_STATUS . " ", "", $taintedData));
                break;

            default:
                throw new Hobis_Exception('Invalid $type (' . $type . ')');
        }

        return $taintedData;
    }

    /**
     * Wrapper method for getting config file
     *
     * @return string
     */
    protected static function getConfig()
	{
        return Hobis_Api_Directory_Package::fromArray(
            array(
                Hobis_Api_Environment_Package::getAppConfigPath(),
                'mail',
                'config.yml'
            )
        );
	}
}

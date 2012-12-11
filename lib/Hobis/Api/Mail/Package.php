<?php

class Hobis_Api_Mail_Package
{
	/**
	 * Singleton for contextHosts
	 */
	protected static $contextHosts;

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
		if (!Hobis_Api_Array_Package::populatedKey('contextId', $options)) {
			throw new Exception(sprintf('Invalid $options[contextId]: %s', serialize($options)));
		}

        if (false === Hobis_Api_Array_Package::populated(self::$contextHosts)) {

            $settings = sfYaml::load(self::getConfig());

            if (false ===  Hobis_Api_Array_Package::populatedKey('contexts', $settings)) {
                throw new Hobis_Api_Exception(sprintf('Invalid $settings: %s', serialize($settings)));
            }

            self::$contextHosts = $settings['contexts'];
        }

        if (!Hobis_Api_Array_Package::populatedKey($options['contextId'], self::$contextHosts)) {
			throw new Exception(sprintf('ContextId mismatch: %s', serialize($options)));
		}

        $transportHost = sprintf('mail.%s', self::$contextHosts[$options['contextId']]);

		$transport = new Zend_Mail_Transport_Smtp($transportHost);

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

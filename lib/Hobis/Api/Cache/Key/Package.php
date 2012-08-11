<?php

class Hobis_Api_Cache_Key_Package
{
    /**
     * Wrapper method for generating a cache key
     *
     * @param string
     * @param array
     * @return string
     */
    public static function generate($staticPrefix, array $dynamicSuffixes = array())
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($staticPrefix)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $staticPrefix: %s', $staticPrefix));
        }

        $key = $staticPrefix;

        // NOTE: Order of entry in array is CRUCIAL
        //  If api call has elements in differing order than another api call it WILL
        //  result in a new key construct
        if (Hobis_Api_Array_Package::populated($dynamicSuffixes)) {
            $key .= Hobis_Api_Cache_Key::SEPARATOR . implode(Hobis_Api_Cache_Key::SEPARATOR, $dynamicSuffixes);
        }

        $key = Hobis_Api_String_Package::tokenize(array('value' => $key, 'separator' => Hobis_Api_Cache_Key::SEPARATOR, 'allowedChars' => array(Hobis_Api_Cache_Key::SEPARATOR)));

        self::validate($key);

        return $key;
    }

    /**
     * Factory method responsible for instantiating and preparing cacheKey objects
     *
     * @param array $options
     * @return $object
     * @throws Hobis_Api_Exception
     */
    public static function factory(array $options)
    {
        // Validate
        if (!Hobis_Api_Array_Package::populatedKey('staticPrefix', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid staticPrefix: %s', serialize($options)));
        }

        //-----
        //Localize
        //-----
        $dynamicSuffixes    = (Hobis_Api_Array_Package::populatedKey('dynamicSuffixes', $options)) ? $options['dynamicSuffixes'] : array();
        $expiry             = (Hobis_Api_Array_Package::populatedKey('expiry', $options)) ? $options['expiry'] : Hobis_Api_Cache::EXPIRY_DEFAULT;
        $staticPrefix       = (Hobis_Api_Array_Package::populatedKey('staticPrefix', $options)) ? $options['staticPrefix'] : null;
        $value              = (Hobis_Api_Array_Package::populatedKey('value', $options)) ? $options['value'] : null;
        //-----

        $key = new Hobis_Api_Cache_Key();

        $key->setExpiry($expiry);
        $key->setKey(self::generate($staticPrefix, $dynamicSuffixes));

        if (Hobis_Api_String_Package::populated($value)) {
            $key->setValue($value);
        }

        return $key;
    }

    /**
     * Wrapper method for logging cache key statuses
     *
     * @return object
     */
    public static function toStatusLog()
    {
		Hobis_Api_Log_Package::registerLogger(Hobis_Api_Cache_Key::LOG_NAME_STATUS, Hobis_Api_Cache_Key::LOG_URI_STATUS);

		return Hobis_Api_Log_Package::getLogger(Hobis_Api_Cache_Key::LOG_NAME_STATUS);
	}

    /**
     * Wrapper method for validating a cache key
     *
     * @param string
     * @throws Hobis_Api_Exception
     */
    public static function validate($key)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_String_Package::populated($key)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $key: %s', $key));
        }

        if (false === Hobis_Api_String_Package::isAlphaNumeric($key, array(Hobis_Api_Cache_Key::SEPARATOR))) {
            throw new Hobis_Api_Exception(sprintf('Cache key is invalid: %s', $key));
        }
        //-----
    }
}
<?php

class Hobis_Api_Crypt_Package
{
    const ENCRYPT_TWO_WAY_CIPHERNAME    = MCRYPT_BLOWFISH;
    const ENCRYPT_TWO_WAY_KEY           = 'I has the powers!';
    const ENCRYPT_TWO_WAY_MODE          = MCRYPT_MODE_ECB;

    /**
     * Factory for creating crypt descriptors
     *
     * @return resoure
     * @throws Hobis_Api_Exception
     */
    protected static function cryptDescriptorFactor()
    {
        $cryptDescriptor = mcrypt_module_open(self::ENCRYPT_TWO_WAY_CIPHERNAME, '', self::ENCRYPT_TWO_WAY_MODE, '');

        if ($cryptDescriptor === false) {
            throw new Hobis_Api_Exception('Invalid $cryptDescriptor');
        }

        return $cryptDescriptor;
    }

    /**
     * Factory for creating initialization vector
     *
     * @param resource $cryptDescriptor
     * @return string
     * @throws Hobis_Api_Exception
     */
    protected static function ivFactory($cryptDescriptor)
    {
        if (!is_resource($cryptDescriptor)) {
            throw new Hobis_Api_Exception('Invalid $cryptDescriptor');
        }

        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($cryptDescriptor));

        if ($iv === false) {
            throw new Hobis_Api_Exception('Invalid $iv');
        }

        return $iv;
    }

    /**
     * Factory for creating a key
     *
     * @param resource $cryptDescriptor
     * @return string
     * @throws Hobis_Api_Exception
     */
    protected static function keyFactory($cryptDescriptor)
    {
        if (!is_resource($cryptDescriptor)) {
            throw new Hobis_Api_Exception('Invalid $cryptDescriptor');
        }

        $key = substr(md5(self::ENCRYPT_TWO_WAY_KEY), 0, mcrypt_enc_get_key_size($cryptDescriptor));

        return $key;
    }

    /**
     * Wrapper method for two-way decrypting an encrypted string
     *
     * @param string $encryptedString
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function twoWayDecrypt($encryptedString)
    {
        if (!Hobis_Api_String_Package::populated($encryptedString)) {
            throw new Hobis_Api_Exception('Invalid $encryptedString');
        }

        $cryptDescriptor    = self::cryptDescriptorFactor();
        $iv                 = self::ivFactory($cryptDescriptor);
        $key                = self::keyFactory($cryptDescriptor);

        mcrypt_generic_init($cryptDescriptor, $key, $iv);

        $string = mdecrypt_generic($cryptDescriptor, $encryptedString);

        mcrypt_generic_deinit($cryptDescriptor);

        return $string;

    }

    /**
     * Wrapper method for two-way encrypting a string
     *
     * @param string $string
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function twoWayEncrypt($string)
    {
        if (!Hobis_Api_String_Package::populated($string)) {
            throw new Hobis_Api_Exception('Invalid $string');
        }

        $cryptDescriptor    = self::cryptDescriptorFactor();
        $iv                 = self::ivFactory($cryptDescriptor);
        $key                = self::keyFactory($cryptDescriptor);

        mcrypt_generic_init($cryptDescriptor, $key, $iv);

        $encryptedString = mcrypt_generic($cryptDescriptor, $string);

        mcrypt_generic_deinit($cryptDescriptor);

        return $encryptedString;
    }
}
<?php

class Hobis_Api_I18N_Package
{
    /**
     * Wrapper method for validating a language code
     *
     * @param string
     * @throws Hobis_Api_Exception
     */
    public static function validateLanguageCode($languageCode)
    {
        //-----
        // Attempt to instantiate new dbo
        //-----
        $languageSettings = sfYaml::load(self::getLanguageConfigFilename());

        if ((!Hobis_Api_Array_Package::populated($languageSettings)) ||
            (!Hobis_Api_Array_Package::populatedKey('codes', $languageSettings))) {
            throw new Hobis_Api_Exception('Invalid $languageSettings');
        }

        $languageCodes = $languageSettings['codes'];

        if (!Hobis_Api_Array_Package::populatedKey($languageCode, $languageCodes)) {
            throw new Hobis_Api_Exception('Invalid $languageCode');
        }
    }

    /**
     * Convience method for getting database yaml file
     *
     * @return path
     */
    protected static function getLanguageConfigFilename()
    {
        return realpath(dirname(__FILE__)) . '/../../../../etc/i18n/language/config.yml';
    }
}

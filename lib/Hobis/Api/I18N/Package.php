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
        $settings = sfYaml::load(self::getConfig());

        if ((!Hobis_Api_Array_Package::populated($settings)) ||
            (!Hobis_Api_Array_Package::populatedKey('codes', $settings))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $settings: %s', serialize($settings)));
        }

        $languageCodes = $settings['codes'];

        if (!Hobis_Api_Array_Package::populatedKey($languageCode, $languageCodes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $languageCode: %s', $languageCode));
        }
    }

    /**
     * Convience method for getting database yaml file
     *
     * @return path
     */
    protected static function getConfig()
    {
        return Hobis_Api_Directory_Package::fromArray(
            array(
                Hobis_Api_Environment_Package::getAppConfigPath(),
                'i18n',
                'config.yml'
            )
        );
    }
}

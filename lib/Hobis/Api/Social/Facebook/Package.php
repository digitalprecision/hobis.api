<?php

class Hobis_Api_Social_Facebook_Package
{
    /**
     * Wrapper method for returning facebook js sdk
     *
     * @param array
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function getJsSdk(array $options)
    {
        //-----
        // Localize
        //-----
        $domain         = (Hobis_Api_Array_Package::populatedKey('domain', $options)) ? $options['domain'] : null;
        $languageCode   = (Hobis_Api_Array_Package::populatedKey('languageCode', $options)) ? $options['languageCode'] : null;
        $siteId         = (Hobis_Api_Array_Package::populatedKey('siteId', $options)) ? $options['siteId'] : null;
        //-----

        //-----
        // Validate
        //-----
        if (false === Hobis_Api_String_Package::populated($domain)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $domain: %s', serialize($domain)));
        } elseif (false === Hobis_Api_String_Package::populatedNumeric($siteId)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $siteId: %s', serialize($siteId)));
        }

        Hobis_Api_I18N_Package::validateLanguageCode($languageCode);
        //-----

        $env = Hobis_Api_Environment_Package::getValue(Hobis_Api_Environment::VAR_LABEL_SERVICE);

        //-----
        // Load config settings
        //-----
        $settings = sfYaml::load(self::getConfig());

        if (false === Hobis_Api_Array_Package::populatedKey($siteId, $settings)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $settings[siteId]: %s', serialize($settings)));
        } elseif (false === Hobis_Api_String_Package::populatedNumeric($settings[$siteId][Hobis_Api_Social_Facebook::APP_ID][$env])) {
            throw new Hobis_Api_Exception(sprintf('Invalid $settings[APP_ID]: %s', serialize($settings)));
        }
        //-----

        // Kiss
        $appId = $settings[$siteId][Hobis_Api_Social_Facebook::APP_ID][$env];

        return <<<SDK
            window.fbAsyncInit = function() {
                FB.init({
                  appId      : '$appId', // App ID
                  channelURL : '//$domain/channel.html', // Channel File
                  status     : true, // check login status
                  cookie     : true, // enable cookies to allow the server to access the session
                  oauth      : true, // enable OAuth 2.0
                  xfbml      : true  // parse XFBML
                });

                // Additional initialization code here
              };

              // Load the SDK Asynchronously
              (function(d){
                 var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                 js = d.createElement('script'); js.id = id; js.async = true;
                 js.src = "//connect.facebook.net/$languageCode/all.js";
                 d.getElementsByTagName('head')[0].appendChild(js);
               }(document));
SDK;
    }

    /**
     * Wrapper method for getting FB root element
     *
     * @return string
     */
    public static function getRootElement()
    {
        return '<div id="fb-root"></div>';
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
                'social',
                'facebook',
                'config.yml'
            )
        );
    }
}

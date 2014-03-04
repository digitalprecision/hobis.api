<?php

class Hobis_Api_Navigation_Package
{
    /**
     * Wrapper method for creating a navigation object from a config
     *
     * @param array
     * @param string
     * @return \Zend_Navigation
     * @throws Hobis_Api_Exception
     */
    public static function factory(array $uriParts, $configAnchor = 'nav')
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populated($uriParts)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $uriParts: %s', serialize($uriParts)));
        } elseif (!Hobis_Api_String_Package::populated($configAnchor)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $configAnchor: %s', $configAnchor));
        }
        //-----

        array_unshift($uriParts, Hobis_Api_Environment_Package::getAppConfigPath());

        $fileUri = Hobis_Api_Directory_Package::fromArray($uriParts);
		
        // Note: we have to load yml natively through sfYaml, if we try to use Zend_Config_Yaml it won't
        //  parse embedded php correctly due to file_get_contents call (should be ob_*)
        $settings = sfYaml::load($fileUri);
		
        if (false === Hobis_Api_Array_Package::populatedKey($configAnchor, $settings)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $configAnchor: %s', serialize($settings)));
        }
		
		//var_dump($settings); exit;

        $config = new Zend_Config($settings[$configAnchor]);
		
        $nav = new Hobis_Api_Navigation($config);
		
		$nav->setContainerType($settings['containerType']);
        $nav->setBrandSettings($settings['brand']);

        return $nav;
    }

    /**
     * Wrapper method for determing if we should skip an element during htmlify
     *
     * @param object
     * @param bool
     * @param array
     * @return boolean
     */
    public static function skipElement(Zend_Navigation_Page_Uri $element, $userIsAuthenticated, array $userCredentials)
    {
        $renderIf = (isset($element->renderIf)) ? $element->renderIf : array();

        if (false === Hobis_Api_Array_Package::populated($renderIf)) {
            return false;
        }

        //-----
        // Lilo (logged in/out)
        //-----
        if (true === Hobis_Api_Array_Package::populatedKey('lilo', $renderIf)) {

            $setting = $renderIf['lilo'];

            if ((('in' === $setting) && (false === $userIsAuthenticated)) ||
                (('out' === $setting) && (true === $userIsAuthenticated))) {
                return true;
            }

            return false;
        }
        //-----

        //-----
        // Perms
        //  Perms are meant to be agnostic, they are dependent upon how the calling code sets them up
        //      They can be either group ids, role ids, a combination thereof, all we care about is the id
        //-----
        if (true === Hobis_Api_Array_Package::populatedKey('perm', $renderIf)) {

            // Perm assumes user must be authenticated
            if (false === $userIsAuthenticated) {
                return true;
            }

            $setting = $renderIf['perm'];

            $action     = Hobis_Api_Array_Package::populatedKey('action', $setting) ? $setting['action'] : 'allow';
            $permIds    = Hobis_Api_Array_Package::populatedKey('permIds', $setting) ? $setting['permIds'] : array();
            $membership = Hobis_Api_Array_Package::populatedKey('membership', $setting) ? $setting['membership'] : 'atLeastOne';

            if (count($permIds) < 1) {
                return true;
            }

            if ('allow' === $action) {

                if ('atLeastOne' === $membership) {

                    if (count(array_intersect($permIds, $userCredentials)) < 1) {
                        return true;
                    }
                }
            }

            elseif ('deny' === $action) {

                if ('atLeastOne' === $membership) {

                    if (count(array_intersect($permIds, $userCredentials)) > 0) {
                        return true;
                    }
                }
            }

            return false;
        }
        //-----

        return false;
    }
}

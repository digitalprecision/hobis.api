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
    public static function factory(array $uriParts, $configAnchor)
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
        $navSettings = sfYaml::load($fileUri);

        if (false === Hobis_Api_Array_Package::populatedKey($configAnchor, $navSettings)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $configAnchor: %s', serialize($navSettings)));
        }

        $config = new Zend_Config($navSettings[$configAnchor]);

        $nav = new Zend_Navigation($config);

        return $nav;
    }

    /**
     * Wrapper method for converting a navigation object to html
     *
     * @param object
     * @param array
     * @return type
     */
    public static function htmlify(Zend_Navigation $nav, array $options)
    {
        $linkFunction           = Hobis_Api_Array_Package::populatedKey('linkFunction', $options) ? $options['linkFunction'] : null;
        $userIsAuthenticated    = Hobis_Api_Array_Package::populatedKey('userIsAuthenticated', $options) ? (bool) $options['userIsAuthenticated'] : false;
        $userCredentials        = Hobis_Api_Array_Package::populatedKey('userCredentials', $options) ? $options['userCredentials'] : array();

        $html = null;

        $html .= sprintf('<ul class="nav_primary_wrap floatClear">%s', PHP_EOL);

        foreach ($nav as $parent) {

            if (true === self::skipElement($parent, $userIsAuthenticated, $userCredentials)) {
                continue;
            }

            $link = ((stripos($parent->getUri(), 'none') !== false) || (false === is_callable($linkFunction))) ? $parent->getLabel() : $linkFunction($parent->getLabel(), $parent->getUri());

            $html .= sprintf('<li class="nav_parent"><span class="nav_header">%s</span>%s', $link, PHP_EOL);

            if ($parent->hasPages()) {

                $html .= sprintf('<ul class="nav_child_wrap">%s', PHP_EOL);

                foreach ($parent->getPages() as $child) {

                    if (true === self::skipElement($child, $userIsAuthenticated, $userCredentials)) {
                        continue;
                    }

                    $link = ((stripos($child->getUri(), 'none') !== false) || (false === is_callable($linkFunction))) ? $child->getLabel() : $linkFunction($child->getLabel(), $child->getUri());

                    $html .= sprintf('<li class="nav_child">%s</li>%s', $link, PHP_EOL);
                }

                $html .= sprintf('</ul>%s', PHP_EOL);
            }

            $html .= sprintf('</li>%s', PHP_EOL);
        }

        $html .= sprintf('</ul>%s', PHP_EOL);

        return $html;
    }

    /**
     * Wrapper method for rendering a menu based on options
     *
     * @param array $options
     * @return type
     */
    public static function renderMenu(array $options)
    {
        // No need to validate here, lower calls will do that for us

        $configAnchor   = Hobis_Api_Array_Package::populatedKey('configAnchor', $options) ? $options['configAnchor'] : 'nav';
        $uriParts       = Hobis_Api_Array_Package::populatedKey('uriParts', $options) ? $options['uriParts'] : array();

        $nav = self::factory($uriParts, $configAnchor);

        return self::htmlify($nav, $options);
    }

    /**
     * Wrapper method for determing if we should skip an element during htmlify
     *
     * @param object
     * @param bool
     * @param array
     * @return boolean
     */
    protected static function skipElement(Zend_Navigation_Page_Uri $element, $userIsAuthenticated, array $userCredentials)
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
        // Group
        //-----
        if (true === Hobis_Api_Array_Package::populatedKey('group', $renderIf)) {
            
            // Group membership assumes user must be authenticated
            if (false === $userIsAuthenticated) {
                return true;
            }
            
            $setting = $renderIf['group'];
            
            $action     = Hobis_Api_Array_Package::populatedKey('action', $setting) ? $setting['action'] : 'allow';
            $groups     = Hobis_Api_Array_Package::populatedKey('groups', $setting) ? $setting['groups'] : array();
            $membership = Hobis_Api_Array_Package::populatedKey('membership', $setting) ? $setting['membership'] : 'atLeastOne';
            
            if (count($groups) < 1) {
                return false;
            }
            
            if ('allow' === $action) {
            
                if ('atLeastOne' === $membership) {
                    
                    if (count(array_intersect($groups, $userCredentials)) < 1) {
                        return true;
                    }
                }
            }
            
            elseif ('disallow' === $action) {
                
                if ('atLeastOne' === $membership) {
                    
                    if (count(array_intersect($groups, $userCredentials)) > 0) {
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
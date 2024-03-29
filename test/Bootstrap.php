<?php

require_once substr(__FILE__, 0, strpos(__FILE__,'/test')) . '/lib/Hobis/Api/Bootstrap.php';

/**
 * This class bootstraps the HobisTest_ system (setups up autoload etc)
 */
class HobisTest_Api_Bootstrap
{
    /**
     * Singleton to determine if bootstrap has already been initalized
     *  This will ensure we don't keep doing the same work multiple times
     *
     * @var bool
     */
    protected static $initialized = false;

    /**
     * Wrapper method for bootstrapping CoreLib
     */
    public static function exec()
    {
        if (!self::$initialized) {

            $bootstrap = new HobisTest_Api_Bootstrap();

            $bootstrap->initIncludePaths();
            $bootstrap->initAutoload();
            $bootstrap->registerAppConfigPath();

            self::$initialized = true;

            Hobis_Api_Log_Package::toErrorLog()->debug('HobisTest_Api_Bootstrap initalized (should only happen once)');
        }
    }

    /**
     * Wrapper method for registering autoloaders
     */
    protected function initAutoload()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();

        $autoloader->registerNamespace(
            array(
                'HobisTest_Api_Module_',
                'HobisTest_Api_Flow_',
            )
        );
    }

    /**
     * Normally this app config path registration doesn't occur at this (the api) layer, but for testing we need to
     *  register an app path with test configs
     */
    protected function registerAppConfigPath()
    {
        $configPath = substr(__FILE__, 0, strpos(__FILE__, '/test')) . '/test/etc';

        Hobis_Api_Environment_Package::setAppConfigPath($configPath);
    }

   /**
    * Wrapper method for initializing include paths
    */
    protected function initIncludePaths()
    {
        // Hardcoding test root dir, so there are no path collisions
        $rootPath = substr(__FILE__, 0, strpos(__FILE__, '/test')) . '/test/lib';

        $potentialIncludePaths = array(
            $rootPath
        );

        $existingIncludePaths = array_filter(explode(':', get_include_path()));

        $includePaths = array_unique(array_merge($potentialIncludePaths, $existingIncludePaths));

        if (!set_include_path(implode(':', $includePaths))) {
            throw new Hobis_Api_Exception(sprintf('Unable to set include path(s)s: %s', serialize($includePaths)));
        }
    }
}

HobisTest_Api_Bootstrap::exec();
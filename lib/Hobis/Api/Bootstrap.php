<?php

/**
 * This class bootstraps the Hobis_Api system (setups up autoload etc)
 */
class Hobis_Api_Bootstrap
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

            $bootstrap = new Hobis_Api_Bootstrap();

            $bootstrap->initIncludePaths();
            $bootstrap->initAutoload();
            $bootstrap->initEncoding();
            $bootstrap->initLoggers();

            self::$initialized = true;

            Hobis_Api_Log_Package::toErrorLog()->debug('Hobis_Api_Bootstrap initalized (should only happen once)');
        }
    }

    /**
     * Wrapper method for registering autoloaders
     */
    protected function initAutoload()
    {
        require_once 'Zend/Loader/Autoloader.php';

        $autoloader = Zend_Loader_Autoloader::getInstance();

        $autoloader->registerNamespace(
            array(
                'Apache_Solr_',
                'Cake_',
                'Hobis_Api_',
                'Horde_',
                'Mobile_',
                'sfYaml'
            )
        );
    }

    /**
     * Wrapper method for setting encoding types
     */
    protected function initEncoding()
    {
        mb_internal_encoding(Hobis_Api_Environment::CHAR_ENCODING_TYPE);
        mb_regex_encoding(Hobis_Api_Environment::CHAR_ENCODING_TYPE);
    }

    /**
     * Wrapper method for initalizing global loggers
     */
    protected function initLoggers()
    {
        Hobis_Api_Log_Package::registerLogger(); // Defaults to Std Out
        Hobis_Api_Log_Package::registerLogger(Hobis_Api_Log::NAME_PHP_ERROR, Hobis_Api_Log::URI_PHP_ERROR);
    }

    /**
     * Wrapper method for initializing include paths
     */
    protected function initIncludePaths()
    {
        $rootPath = substr(__FILE__, 0, strpos(__FILE__, '/lib')) . '/lib';

        // Note: If vendor app is not namespaced correctly, add to list,
        //  otherwise if namespaced correctly, having $rootPath will be enough for autoloader to work
        $potentialIncludePaths = array(
            $rootPath,
            $rootPath . DIRECTORY_SEPARATOR . 'FastImage',
            $rootPath . DIRECTORY_SEPARATOR . 'MobileDetect',
            $rootPath . DIRECTORY_SEPARATOR . 'PHPThumb',
            $rootPath . DIRECTORY_SEPARATOR . 'PHPMarkdown',
            $rootPath . DIRECTORY_SEPARATOR . 'SFComponent'
        );

        $existingIncludePaths = array_filter(explode(':', get_include_path()));

        $includePaths = array_unique(array_merge($potentialIncludePaths, $existingIncludePaths));

        if (!set_include_path(implode(':', $includePaths))) {
            throw new Hobis_Api_Exception(sprintf('Unable to set include path(s)s: %s', serialize($includePaths)));
        }
    }
}

Hobis_Api_Bootstrap::exec();

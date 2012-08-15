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
                'Hobis_Api_',
                'sfYaml',
                'Cake_'
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
        $rootDir = substr(__FILE__, 0, strpos(__FILE__, sprintf('%slib', DIRECTORY_SEPARATOR))) . sprintf('%slib', DIRECTORY_SEPARATOR);

        // Note: If vendor app is not namespaced correctly, add to list,
        //  otherwise if namespaced correctly, having $rootDir will be enough for autoloader to work
        $potentialIncludeDirs = array(
            $rootDir,
            $rootDir . DIRECTORY_SEPARATOR . 'SFComponent',
            $rootDir . DIRECTORY_SEPARATOR . 'PHPThumb',
            $rootDir . DIRECTORY_SEPARATOR . 'PHPMarkdown'
        );

        $existingIncludeDirs = array_filter(explode(':', get_include_path()));

        $includeDirs = array_unique(array_merge($potentialIncludeDirs, $existingIncludeDirs));

        if (!set_include_path(implode(':', $includeDirs))) {
            throw new Hobis_Api_Exception(sprintf('Unable to set include path(s)s: %s', serialize($includeDirs)));
        }
    }
}

Hobis_Api_Bootstrap::exec();
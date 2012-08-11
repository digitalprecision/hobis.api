<?php

class PHPUnitAutoload
{
    protected static $instance;

    public static function register()
    {
        if (false === spl_autoload_register(array(self::getInstance(), 'autoload'))) {
            throw new Exception(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
        }
    }

    protected static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new PHPUnitAutoload();
        }

        return self::$instance;
    }

    public static function unregister()
    {
        spl_autoload_unregister(array(self::getInstance(), 'autoload'));
    }

    protected function autoload($class)
    {
        $basePath = substr(__FILE__, 0, strpos(__FILE__, '/test')) . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'vendor';

        //-----
        // PHPUnit dev jacked up the namespaces of some of the libs, for example PHP_Timer and PHP_CodeCoverage
        //  would end up in the same folder, which means we would have to ignore versions if we combined both
        //  libs, so rather than deal with that mess, just forced a naming convention which ensures no namespace
        //  collisions exist
        //-----
        if (stripos($class, 'PHP_CodeCoverage') !== false) {
            $class = str_replace('PHP', 'PHPCodeCoverage', $class);
        } elseif (stripos($class, 'PHP_Timer') !== false) {
            $class = str_replace('PHP', 'PHPTimer', $class);
        } elseif (stripos($class, 'File_Iterator') !== false) {
            $class = str_replace('File', 'PHPFileIterator', $class);
        } elseif (stripos($class, 'PHPUnit_Framework_MockObject') !== false) {
            $class = str_replace('PHPUnit_Framework_MockObject', 'PHPMockObject_Framework_MockObject', $class);
        } elseif (stripos($class, 'Text_Template') !== false) {
            $class = str_replace('Text', 'PHPTextTemplate_Text', $class);
        }
        //-----

        $classAsPath = str_ireplace('_', DIRECTORY_SEPARATOR, $class);

        $classUri = $basePath . DIRECTORY_SEPARATOR . $classAsPath . '.php';

        if (is_file($classUri)) {
            require_once $classUri;
        }
    }
}
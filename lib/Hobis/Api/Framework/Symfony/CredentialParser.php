<?php

class Hobis_Api_Framework_Symfony_CredentialParser
{
    const APPS_DIR      = 'apps';
    const CONFIG_DIR    = 'config';
    const MODULES_DIR   = 'modules';

    /**
     * Which symfony application will be parsed
     *
     * @var string
     */
    protected $application;

    /**
     * Path to symfony root directory
     *
     * @var string
     */
    protected $rootDirectory;

    /**
     * Setter for application
     *
     * @param string $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * Setter for root directory
     *
     * @param string $rootDirectory
     */
    public function setRootDirectory($rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * Getter for application
     *
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Getter for root directory
     *
     * @return string
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }


    /**
     * This method will parse a directory tree looking for security.yml files
     *
     */
    public function parse()
    {
        $appDir = Hobis_Api_Directory::fromArray(
            array(
                $this->getRootDirectory(),
                self::APPS_DIR,
                $this->getApplication()
            )
        );

        $appConfigDir = Hobis_Api_Directory::fromArray(
            array(
                $appDir,
                self::CONFIG_DIR
            )
        );

        $modulesDir =
            Hobis_Api_Directory::fromArray(
            array(
                $appDir,
                self::MODULES_DIR
            )
        );

        Hobis_Api_Log::getLogger()->debug('App Dir: ' . $symfonyAppDir, array('method' => __METHOD__, 'line' => __LINE__));
        Hobis_Api_Log::getLogger()->debug('App Dir: ' . $symfonyAppConfigDir, array('method' => __METHOD__, 'line' => __LINE__));
        Hobis_Api_Log::getLogger()->debug('App Dir: ' . $symfonyAppModulesDir, array('method' => __METHOD__, 'line' => __LINE__));

        $appConfig = Hobis_Api_Parser_Yaml::Load(Hobis_Api_Directory::fromArray(array($appConfigDir, 'security.yml')));

        // Need to get main security.yml
        $iterator = new DirectoryIterator($modulesDir);

        foreach ($iterator as $fileInfo) {

            if ((!$fileInfo->isDot()) && ($fileInfo->isDir())) {

            	$moduleConfigUri = Hobis_Api_Directory::fromArray(array($modulesDir, $fileInfo->getFilename(), 'config', 'security.yml'));

            	if (new SplFileInfo($moduleConfigUri)) {
            		$moduleConfigs[$fileInfo->getFilename()] = Hobis_Api_Parser_Yaml::Load($moduleConfigUri);
            	}
            }
        }

        var_dump($moduleConfigs);
    }

    /**
     * Factory method
     *
     * @param array $options
     * @return object
     * @throws Hobis_Api_Exception
     */
    public static function factory($options)
    {
        if (!Hobis_Api_Array::populated($options)) {
            throw new Hobis_Api_Exception('Invalid $options');
        }

        elseif (!Hobis_Api_Array::populatedKey('symfonyDir', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[symfonyDir');
        }

        elseif (!Hobis_Api_Array::populatedKey('symfonyApp', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[symfonyApp]');
        }

        $parser = new Hobis_Api_Framework_Symfony_CredentialParser();

        $parser->setRootDirectory($options['symfonyDir']);
        $parser->setApplication($options['symfonyApp']);

        return $parser;
    }
}
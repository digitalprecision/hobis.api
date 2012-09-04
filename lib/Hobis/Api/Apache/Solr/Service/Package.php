<?php

class Hobis_Api_Apache_Solr_Service_Package
{
    /**
     * Singleton array for storing instances
     *
     * @var array
     */
    protected static $instance = array();

    /**
     * Getter facotry for search engine instance
     *
     * @param string $siteDomain
     * @return object
     */
    public static function getInstance(array $options)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('siteId', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[siteId]');
        } elseif (!Hobis_Api_Array_Package::populatedKey('core', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[core]');
        }
        //-----

        $context    	= (Hobis_Api_Array_Package::populatedKey('context', $options)) ? $options['context'] : Hobis_Api_Apache_Solr::CONTEXT_READONLY;
        $core       	= $options['core'];
        $siteId     	= $options['siteId'];
        $instanceKey	= md5($siteId . '_' . $core . '_' . $context);

        // Use existing if poss
        if ((Hobis_Api_Array_Package::populatedKey($instanceKey, self::$instance)) &&
            (self::$instance[$instanceKey] instanceof Hobis_Api_SolrPhpClient_Service) &&
            (self::$instance[$instanceKey]->getContext() === $context) &&
            (self::$instance[$instanceKey]->getCore() === $core) &&
            (self::$instance[$instanceKey]->ping())) {
            return self::$instance[$instanceKey];
        }

        // Parse config
        $settings = sfYaml::load(self::getConfig());

        //-----
        // Validate required config values
        //-----
        if (!Hobis_Api_Array_Package::populated($settings)) {
        	throw new Hobis_Api_Exception(sprintf('Invalid $settings: %s', serialize($settings)));
        }

		elseif (!Hobis_Api_Array_Package::populatedKey($siteId, $settings)) {
            throw new Hobis_Api_Exception('Invalid config entry $settings[$siteId]');
        }

		elseif (!Hobis_Api_Array_Package::populatedKey($core, $settings[$siteId])) {
            throw new Hobis_Api_Exception('Invalid config entry $settings[$siteId][$context]');
        }

        elseif (!Hobis_Api_Array_Package::populatedKey($context, $settings[$siteId])) {
            throw new Hobis_Api_Exception('Invalid config entry $settings[$siteId][$context]');
        }
        //-----

        $instance = new Hobis_Api_Apache_Solr_Service(
            $settings[$siteId][$context][Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_HOST],
            $settings[$siteId][$context][Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_PORT],
            $settings[$siteId][$core][Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_URL]
        );

        if (!$instance->ping()) {
            throw new Hobis_Api_Exception('Search Engine is offline (could not ping())');
        }

        //-----
        // Set defaults
        //-----
        $context    = (Hobis_Api_Array_Package::populatedKey(Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_CONTEXT, $settings[$siteId])) ? $settings[$siteId[Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_VERSION]] : $settings[Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_DEFAULT][Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_VERSION];
        $version    = (Hobis_Api_Array_Package::populatedKey(Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_VERSION, $settings[$siteId])) ? $settings[$siteId[Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_VERSION]] : $settings[Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_DEFAULT][Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_VERSION];
        $writerType = (Hobis_Api_Array_Package::populatedKey(Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_WRITER_TYPE, $settings[$siteId])) ? $settings[$siteId[Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_WRITER_TYPE]] : $settings[Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_DEFAULT][Hobis_Api_Apache_Solr::CONFIG_FILE_KEY_WRITER_TYPE];

		$instance->setCore($core);
        $instance->setContext($context);
        $instance->setVersion($version);
        $instance->setWriterType($writerType);
        //-----

        self::$instance[$instanceKey] = $instance;

        return $instance;
    }

    /**
     * Getter for source config filename
     *
     * @return string
     */
    protected static function getConfig()
	{
        return Hobis_Api_Directory_Package::fromArray(
            array(
                Hobis_Api_Environment_Package::getAppConfigPath(),
                'searchengine',
                'solr',
                'config.yml'
            )
        );
	}
}

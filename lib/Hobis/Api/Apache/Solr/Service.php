<?php

class Hobis_Api_Apache_Solr_Service extends Apache_Solr_Service
{
	/**
	 * Distinguish among potential multiple cores
	 * 
	 * @var
	 */
	protected $core;
	
    /**
     * Readonly vs Readwrite
     *
     * @var string
     */
    protected $context;

    /**
     * Used in search engine queries to inform search engine which version to use
     *
     * @var string
     */
    protected $version;

    /**
     * Used in search engine queries to inform search engine how to form response
     *
     * @var string
     */
    protected $writerType;

    /**
     * Constructor
     *
     * @param string $host
     * @param int $port
     * @param string $path
     */
    public function __construct($host = 'localhost', $port = 8180, $path = '/solr/')
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_String_Package::populated($host)) {
	        throw new Hobis_Api_Exception(sprintf('Invalid $host (%s)'), $host);
	    }

	    elseif (!Hobis_Api_String_Package::populatedNumeric($port)) {
	        throw new Hobis_Api_Exception('Invalid $port');
	    }

	    elseif (!Hobis_Api_String_Package::populated($path)) {
	        throw new Hobis_Api_Exception('Invalid $path');
	    }
        //-----

        parent::__construct($host, $port, $path);
    }
	
	/**
	 * Setter for core
	 * 
	 * @param string $core
	 */
	public function setCore($core)
	{
		$this->core = $core;
	}

    /**
     * Setter for context
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Setter for version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Setter for writer type
     *
     * @param string $writerType
     */
    public function setWriterType($writerType)
    {
        $this->writerType = $writerType;
    }
	
	/**
	 * Getter for core
	 * 
	 * @return string
	 */
	public function getCore()
	{
		return $this->core;
	}

    /**
     * Getter for context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Getter for version
     *
     * @return string
     */
    protected function getVersion()
    {
        return $this->version;
    }

    /**
     * Getter for writer type
     *
     * @return string
     */
    protected function getWriterType()
    {
        return $this->writerType;
    }

    /**
     * Parent::search() is bad. Should be using just array of params vs 43601 command line args
     *
     * @param string $query
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @param object $method
     * @return array
     */
    public function doSearch(array $params)
	{
	    //-----
	    // Validate
	    //-----
	    if (!Hobis_Api_Array_Package::populated($params)) {
	        throw new Hobis_Api_Exception('Invalid $params');
	    } elseif (!Hobis_Api_Array_Package::populatedKey('q', $params)) {
	        throw new Hobis_Api_Exception('Invalid $params[q]');
	    }
	    //-----

        //-----
        // Set params
        //-----

	    // Set defaults
	    $params['json.nl'] = $this->_namedListTreatment;

	    // Set values derived from config file
        $params['version']  = ($this->getVersion()) ? $this->getVersion() : Hobis_Api_Apache_Solr::DEFAULT_VERSION;
        $params['wt']       = ($this->getWriterType()) ? $this->getWriterType() : Hobis_Api_Apache_Solr::SOLR_DEFAULT_WRITER_TYPE;

		// Set overrides (if avail)
		$params['start']  = (Hobis_Api_Array_Package::populatedKey('start', $params)) ? $params['start'] : Hobis_Api_Apache_Solr::DEFAULT_START;
		$params['rows']   = (Hobis_Api_Array_Package::populatedKey('rows', $params)) ? $params['rows'] : Hobis_Api_Apache_Solr::DEFAULT_ROWS;
		$params['qt']     = (Hobis_Api_Array_Package::populatedKey('qt', $params)) ? $params['qt'] : Hobis_Api_Apache_Solr::DEFAULT_QUERY_TYPE;
		//-----

		// Set method
		$method = (Hobis_Api_Array_Package::populatedKey('method', $params)) ? $params['method'] : parent::METHOD_GET;

		// use http_build_query to encode our arguments because its faster
		// than urlencoding all the parts ourselves in a loop
		$querystring = http_build_query($params, null, $this->_queryStringDelimiter);

		// because http_build_query treats arrays differently than we want to, correct the query
		// string by changing foo[#]=bar (# being an actual number) parameter strings to just
		// multiple foo=bar strings. This regex should always work since '=' will be urlencoded
		// anywhere else the regex isn't expecting it
		$querystring = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $querystring);

		switch ($method) {

		    case parent::METHOD_GET:
                $response = $this->_sendRawGet($this->_searchUrl . $this->_queryDelimiter . $querystring);
                break;

		    case parent::METHOD_POST:
                $response = $this->_sendRawPost($this->_searchUrl, $querystring, FALSE, 'application/x-www-form-urlencoded');
                break;

		    default:
                throw new Hobis_Api_Exception("Unsupported method '$method', please use the Apache_Solr_Service::METHOD_* constants");
		}

		// Don't throw exception
		//    Want to give calling code access to querystring etc
		//    Calling code is expecting an object, so give it one

		$response->setRequestType($method);
		$response->setQuerystring($querystring);

		return $response;
	}

	/**
	 * Parent version of this method is just a wrapper method for issuing query
	 *     to search engine. Overriding so I can customize response object
	 *
	 * @param string $url
	 * @param bool $timeout
	 * @return object
	 */
	protected function _sendRawGet($url, $timeout = FALSE)
	{
		// set the timeout if specified
		if ($timeout !== FALSE && $timeout > 0.0) {

			// timeouts with file_get_contents seem to need
			// to be halved to work as expected
			$timeout = (float) $timeout / 2;

			stream_context_set_option($this->_getContext, 'http', 'timeout', $timeout);
		}

		else {
			// use the default timeout pulled from default_socket_timeout otherwise
			stream_context_set_option($this->_getContext, 'http', 'timeout', $this->_defaultTimeout);
		}

		//$http_response_header is set by file_get_contents
		//$response = new Apache_Solr_Response(@file_get_contents($url, false, $this->_getContext), $http_response_header, $this->_createDocuments, $this->_collapseSingleValueArrays);
		// !!Custom
		$response = new Hobis_Api_Apache_Solr_Response(@file_get_contents($url, false, $this->_getContext), $http_response_header, $this->_createDocuments, $this->_collapseSingleValueArrays);

		return $response;
	}
}
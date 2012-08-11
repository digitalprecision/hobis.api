<?php

class Hobis_Api_Apache_Solr_Response extends Apache_Solr_Response
{
    /**
     * Querystring issued to search engine
     *
     * @var string
     */
    protected $querystring;

    /**
     * How query was issued (get|post)
     *
     * @var string
     */
    protected $requestType;

    /**
     * Setter for querystring
     *
     * @param string $querystring
     */
    public function setQuerystring($querystring)
    {
        $this->querystring = $querystring;
    }

    /**
     * Getter for querytype
     *
     * @param string $requestType
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;
    }

    /**
     * Getter for querystring
     *
     * @return string
     */
    public function getQuerystring()
    {
        return $this->querystring;
    }

    /**
     * Getter for querytype
     *
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Constructor
     *
     * @param array $rawResponse
     * @param array $httpHeaders
     * @param bool $createDocuments
     * @param bool $collapseSingleValueArrays
     */
    public function __construct($rawResponse, $httpHeaders = array(), $createDocuments = true, $collapseSingleValueArrays = true)
	{
        parent::__construct($rawResponse, $httpHeaders, $createDocuments, $collapseSingleValueArrays);
	}
}
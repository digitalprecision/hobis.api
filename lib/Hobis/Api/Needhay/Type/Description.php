<?php

/**
 * Descriptions are made up of contents of a description file (on haystack)
 *  There was a requirement for the ability to store data to this file
 *  where data could be separated by type, and put back together again upon read
 */
class Hobis_Api_Needhay_Type_Description
{
    /**
     * Tokens separating description data with the haystack file
     */
    const TOKEN_TEXT        = 'tokenText';
    const TOKEN_SOURCE_ID   = 'tokenSourceId';
    const TOKEN_SOURCE_URL  = 'tokenSourceUrl';
    const TOKEN_TIMESTAMP   = 'tokenTimestamp';

    // Filename of haystack asset
    const ASSET_NAME    = 'description.txt';

    /**
     * Primary id of a description
     *
     * @var int
     */
    protected $id;

    /**
     * Id used to identify which source provided the text information
     *
     * @var int
     */
    protected $sourceId;

    /**
     * Url to source which provided the text information
     * @var type
     */
    protected $sourceUrl;

    /**
     * Description text
     *
     * @var string
     */
    protected $text;

    /**
     * Timestamp reflecting time of last mod
     *
     * @var int
     */
    protected $timestamp;

    /**
     * Setter for description id
     *
     * @param int
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Setter for source id
     *
     * @param int
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * Setter for source url
     *
     * @param string
     */
    public function setSourceUrl($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * Setter for text
     *
     * @param string
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Setter for timestamp
     *
     * @param int
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Getter for source id
     *
     * @return int
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Getter for source url
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * Getter for text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Getter for timestamp
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}

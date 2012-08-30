<?php

/**
 * Descriptions are made up of contents of a description file (on haystack)
 *  There was a requirement for the ability to store data to this file
 *  where data could be separated by type, and put back together again upon read
 */
class Hobis_Api_Needhay_Type_Description
{
    /**
     * Tokens are used to separate various description attributes
     */
    const TOKEN_ID          = 'Id';
    const TOKEN_TEXT        = 'Text';
    const TOKEN_SOURCE_ID   = 'SourceId';
    const TOKEN_SOURCE_URL  = 'SourceUrl';
    const TOKEN_TIMESTAMP   = 'Timestamp';

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

    /**
     * Wrapper method for converting this object to an array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::TOKEN_ID          => $this->getId(),
            self::TOKEN_TEXT        => $this->getText(),
            self::TOKEN_SOURCE_ID   => $this->getSourceId(),
            self::TOKEN_SOURCE_URL  => $this->getSourceUrl(),
            self::TOKEN_TIMESTAMP   => $this->getTimestamp()
        );
    }
}
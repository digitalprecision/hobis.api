<?php

require_once 'ThumbLib.inc.php';

class Hobis_Api_Image_Resizer_Package
{
    /**
     * Wrapper method for creating instance of resizer api
     *
     * @param null $filename
     * @param array $options
     * @param bool $isDataStream
     * @return mixed
     */
    public static function create(array $params)
    {
        // Validate
        if (false === Hobis_Api_Array_Package::populatedKey('source_file_uri', $params)) {
            throw new Hobis_Api_Exception(sprintf('Invalid source_file_uri: %s', serialize($params)));
        }

        $isDataStream   = Hobis_Api_Array_Package::populatedKey('is_data_stream', $params, true) ? true : false;
        $options        = Hobis_Api_Array_Package::populatedKey('options', $params) ? $params['options'] : array();
        $sourceFileUri  = $params['source_file_uri'];

        return PhpThumbFactory::create($sourceFileUri, $options, $isDataStream);
    }
}
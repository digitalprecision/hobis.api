<?php

abstract class Hobis_Api_Needhay_Store_Op_File_Xml extends Hobis_Api_Needhay_Store_Op_File
{
    const ENCODING          = 'UTF-8';
    const FILE_EXTENSION    = 'xml';
    const TAB               = '    ';
    const VERSION           = '1.0';

    const TAG_CONTEXT       = 'context';
    const TAG_ID            = 'id';
    const TAG_META          = 'meta';
    const TAG_OBJECT        = 'object';
    const TAG_POINTER       = 'pointer';
    const TAG_POINTERS      = 'pointers';
    const TAG_ROOT          = 'needle';
    const TAG_ASSET_NAME    = 'assetName';

    /**
     * Factory method for getting a reader
     *
     * @return object
     * @throws Hobis_Api_Exception
     */
    protected function getReader()
    {
        $reader = new Hobis_Api_Xml_Reader();

        if (!@$reader->open($this->getNeedleFileUri())) {

            $e = new Hobis_Api_Exception(sprintf('Could not open %s for parsing', $this->getNeedleFileUri()), Hobis_Api_Exception::CODE_XML_READER_UNABLE_TO_OPEN);

            throw $e;
        }

        elseif (!@$reader->read()) {

            $e = new Hobis_Api_Exception(sprintf('%s is not a valid xml file', $this->needleFileUri), Hobis_Api_Exception::CODE_XML_READER_UNABLE_TO_READ);

            throw $e;
        }

        return $reader;
    }

    /**
     * Factory method for getting a writer
     *
     * @return object
     */
    protected function getWriter()
    {
        return new Hobis_Api_Xml_Writer();
    }
}
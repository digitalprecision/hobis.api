<?php

class Hobis_Api_Exception extends Exception
{
    const EXCEPTION_FORMAT = '%s (%s): %s';

    const CODE_FILE_DNE                 = 100;
    const CODE_DIR_BASE_EQUALS_REMOVE   = 101;

    const CODE_XML_READER_UNABLE_TO_OPEN = 200;
    const CODE_XML_READER_UNABLE_TO_READ = 201;

    /**
     * Overriding string method to match our format
     *
     * @return unknown
     */
    public function __toString()
    {
        return sprintf(self::EXCEPTION_FORMAT, $this->file, $this->line, $this->message);
    }
}
<?php

class Hobis_Api_Apache_Solr
{
    const CONTEXT_READONLY  = 'readonly';
    const CONTEXT_READWRITE = 'readwrite';

    const CONFIG_FILE_KEY_CONTEXT       = 'context';
    const CONFIG_FILE_KEY_DEFAULT       = 'default';
    const CONFIG_FILE_KEY_HOST          = 'host';
    const CONFIG_FILE_KEY_MAX_ROWS      = 'max_rows';
    const CONFIG_FILE_KEY_PORT          = 'port';
    const CONFIG_FILE_KEY_URL           = 'url';
    const CONFIG_FILE_KEY_VERSION       = 'version';
    const CONFIG_FILE_KEY_WRITER_TYPE   = 'writer_type';

    const DEFAULT_PAGE             = 1;
    const DEFAULT_ROWS             = 10;
    const DEFAULT_START            = 0;
    const DEFAULT_QUERY_TYPE       = 'standard';
    const DEFAULT_VERSION          = 2.2;
    const DEFAULT_WRITER_TYPE      = 'phps';
}
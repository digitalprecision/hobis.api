<?php

class Hobis_Api_Password
{
    // These constants may be changed without breaking existing hashes.
    const HASH_ALGORITHM        = 'sha512';
    const HASH_SECTION_COUNT    = 4;
    
    const HASH_SECTION_INDEX_ALGORITHM          = 0;
    const HASH_SECTION_INDEX_DERIVED_KEY        = 3;
    const HASH_SECTION_INDEX_ITERATION_COUNT    = 1;
    const HASH_SECTION_INDEX_SALT               = 2;    

    const ITERATION_RANGE_LOW    = 2000;
    const ITERATION_RANGE_HIGH   = 10000;

    const LENGTH_BYTES_SALT          = 24;
    const LENGTH_BYTES_DERIVED_KEY   = 24;
}
<?php

class Hobis_Api_Needhay
{
    /**
     * Abstract constants
     *  These values help with determining how far down to generate a path for accessing:
     *      Needle: If adapter is set to file
     *      Haystack: All adapters share the same haystack
     *  Kept abstract in case they need to be used in other contexts
     */
    const CONTEXT = 'context';
    const OBJECT = 'object';
    const NEEDLE = 'needle';
    const HAYSTACK = 'haystack';
}
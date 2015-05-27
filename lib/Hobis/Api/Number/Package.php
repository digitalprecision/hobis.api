<?php

class Hobis_Api_Number_Package
{
    /**
     * Wrapper method for converting int to human readable filesize
     *
     * @param int
     * @param int
     * @return string
     */
    public static function toHumanReadableFilesize($number, $decimals = 2)
    {
        //-----
        // Validate
        //-----
        if (false === Hobis_Api_String_Package::populatedNumeric($number)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $number: %s', serialize($number)));
        } elseif (false === Hobis_Api_String_Package::populatedNumeric($number)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $number: %s', serialize($number)));
        }
        //-----

        $factor         = floor((strlen($number) - 1) / 3);
        $identifiers    = 'BKMGTP';

        return sprintf("%.{$decimals}f", $number / pow(1024, $factor)) . @$identifiers[$factor];
    }
}
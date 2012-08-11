<?php

class Hobis_Api_String_Package
{
    /**
     * Wrapper method for determining if string is alphanumeric
     *  Also has flexibility to overlook allowed chars
     *
     * @param string
     * @param array
     * @return bool
     * @throws Hobis_Api_Exception
     */
    public static function isAlphaNumeric($string, array $allowedChars = array())
    {
        if (!self::populated($string)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $string: %s', $string));
        }

        $regex = 'a-zA-Z0-9';

        if (Hobis_Api_Array_Package::populated($allowedChars)) {
            $regex .= implode($allowedChars);
        }

        return filter_var($string, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[' . $regex . ']+$/')));
    }

    /**
     * Wrapper method for converting array to string using separator as glue
     *
     * @param array
     * @param string
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function fromArray($array, $separator = '_')
    {
        // Validate
        if (!Hobis_Api_Array_Package::populated($array)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $array (%s)', serialize($array)));
        }

        return implode($separator, $array);
    }

    /**
     * Wrapper method for inflecting strings
     *
     * @param string $rootString
     * @param int $howMany
     * @return string
     */
    public static function inflect($string, $howMany)
    {
        return ((int) $howMany > 1) ? Cake_Inflector::pluralize($string) : Cake_Inflector::singularize($string);
    }

    /**
     * Convience method for determining if arg is empty
     *  Did not use empty for checks as some values may intentionally contain 0
     *
     * @param string $var
     * @return bool
     */
    public static function populated($string)
    {
        //-----
        // Handle various datatypes
        //-----

        // Don't want to assume an array as a string, even if we serialize then check
        if (is_array($string)) {
            return false;
        }

        if (is_object($string)) {

            if (!is_callable(array($string, '__toString'))) {
                return false;
            }

            $string = (string) $string;
        }
        //-----

        return (mb_strlen($string) > 0) ? true : false;
    }

    /**
     * Convience method for determining if arg is empty and numeric
     *  Did not use empty for checks as some values may intentionally contain 0
     *
     * @param string $var
     * @return bool
     */
    public static function populatedNumeric($string)
    {
        return ((mb_strlen($string) > 0) && (is_numeric($string))) ? true : false;
    }

    /**
     * Convenience method for tokenizing value
     *
     * @param array
     * @throws Hobis_Api_Exception
     * @return string
     */
    public static function tokenize($params)
    {
        // Validate
        if (!Hobis_Api_Array_Package::populatedKey('value', $params)) {
            throw new Hobis_Api_Exception('Invalid $value');
        }

        $invalidChars = array(
            '[',
            ']'
        );

        foreach ($invalidChars as $invalidChar) {
            if (mb_stripos($params['value'], $invalidChar) !== false) {
                throw new Hobis_Api_Exception(sprintf('(%s) is an invalid char, cannot tokenize', $invalidChar));
            }
        }

        $value		= $params['value'];
        $separator      = (Hobis_Api_Array_Package::populatedKey('separator', $params)) ? $params['separator'] : '-';
        $urlEncode      = (Hobis_Api_Array_Package::populatedKey('urlEncode', $params)) ? $params['urlEncode'] : false;
        $allowedChars	= (Hobis_Api_Array_Package::populatedKey('allowedChars', $params)) ? $params['allowedChars'] : array();
        $charsToRemove	= (Hobis_Api_Array_Package::populatedKey('charsToRemove', $params)) ? $params['charsToRemove'] : array();

        $regex = 'a-zA-Z0-9';

        if (count($allowedChars) > 0) {
            $regex .= implode($allowedChars);
        }

        $value = trim($value);
        $value = html_entity_decode($value, ENT_QUOTES, Hobis_Api_Environment::CHAR_ENCODING_TYPE);
        $value = str_replace(array('"', "'"), '', $value);

        if (Hobis_Api_Array_Package::populated($charsToRemove)) {
            $value = str_ireplace($charsToRemove, '', $value);
        }

        $value = mb_eregi_replace('[^' . $regex . ']', $separator, $value); // remove non alphanum except $allowedChars

        $value = mb_strtolower($value); // lowercase
        $value = ($urlEncode) ? urlencode($value) : $value;

        return $value;
    }
}
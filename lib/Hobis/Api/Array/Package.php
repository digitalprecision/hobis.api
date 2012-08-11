<?php

class Hobis_Api_Array_Package
{
    /**
     * Wrapper method for creating a string from an array with correct list
     *  construction, i.e. "banana, apple, orange" becomes "banana, apple, and orange"
     *
     * @param array $array
     * @param string $glue
     * @return string
     */
    public static function andify(array $array, $glue = ', ')
    {
        return self::toList(
            array(
                'array'         => $array,
                'glue'          => $glue,
                'conjunction'   => 'and'
            )
        );
    }

    /**
     * Wrapper method for casting elements of an array into correct datatypes
     *
     * @param array
     * @return array
     */
    public static function castStrictTypes(array $array)
    {
        // Nice little hack found via php.net comments section for is_numeric function
        //  if the value is considered to be numeric then simply add a 0 to the value, which will correctly
        //  cast the value from string to what it should be
        //  Otherwise strings like '.66' get converted to 0 if cast to int
        return array_map(function($value) { return (is_numeric($value)) ? ($value + 0) : $value; }, $array);
    }

	/**
     * Wrapper method for creating a string from an array with correct list
     *  construction, i.e. "banana, apple, orange" becomes "banana, apple, or orange"
     *
     * @param array $array
     * @param string $glue
     * @return string
     */
    public static function orify(array $array, $glue = ', ')
    {
        return self::toList(
            array(
                'array'         => $array,
                'glue'          => $glue,
                'conjunction'   => 'or'
            )
        );
    }

    /**
     * Wrapper method for converting an array to a list
     *
     * @param array $options
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function toList(array $options)
    {
        if (!Hobis_Api_Array_Package::populatedKey('array', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[array]', serialize($options)));
        }

        $array          = $options['array'];
        $conjunction    = (Hobis_Api_Array_Package::populatedKey('conjunction', $options)) ? $options['conjunction'] : null;
        $glue           = (Hobis_Api_Array_Package::populatedKey('glue', $options)) ? $options['glue'] : ', ';

        if (!Hobis_Api_String_Package::populated($conjunction)) {
            return implode($glue, $array);
        }

        $arrayCount = count($array);

        switch ($arrayCount) {

            case 1:
                return current($array);
                break;

            case 2:
                return current($array) . ' ' . $conjunction . ' ' . next($array);
        }

        $array[($arrayCount - 1)] = $conjunction . ' ' . end($array);

        return implode($glue, $array);
    }

    /**
     * Convenience method for imploding array values which are quoted
     *
     * @param array $array
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function implodeWithQuotes(array $array)
    {
        // Validate
        if (!self::populated($array)) {
            throw new Hobis_Api_Exception('Invalid $array');
        }

        return "'" . implode("','", $array) . "'";
    }

    /**
     * Convenience method for determining if array is populated
     *  DO NOT TYPECHECK arg $array (i.e. array $array)
     *  There are instances where null may be passed in
     *
     * @param array $array
     * @return bool
     */
    public static function populated($array)
    {
        return ((is_array($array)) && (count($array) > 0)) ? true : false;
    }

    /**
     * Convenience method for determining if given key exists within given array
     *    If specific value is passed then this will be checked for as well
     *    Did not use empty for checks as some values may intentionally contain 0
     *  DO NOT TYPECHECK arg $array (i.e. array $array)
     *  There are instances where null may be passed in
     *
     * @param string
     * @param array
     * @param string
     * @param bool
     * @return bool
     */
    public static function populatedKey($key, $array, $specificValue = null, $strict = true)
    {
        if ((is_array($array)) &&
            (array_key_exists($key, $array))) {

            if (is_array($array[$key])) {
                return (count($array[$key]) > 0) ? true : false;
            }

            elseif (is_object($array[$key])) {
                return true;
            }

            elseif (mb_strlen($array[$key]) > 0) {

                if (!is_null($specificValue)) {

                    if ($strict === true) {
                        return ($array[$key] === $specificValue) ? true : false;
                    } else {
                        return ($array[$key] == $specificValue) ? true : false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Wrapper method for shuffling an array where maintaining keys is vital
     *
     * @param array
     * @return array
     */
    public static function shuffle(array $array)
    {
        // Validate
        if (!self::populated($array)) {
            throw new Hobis_Api_Exception('Invalid $array');
        }

        $shuffledArray = array();

        $keys = array_keys($array);

        shuffle($keys);

        foreach ($keys as $key) {
            $shuffledArray[$key] = $array[$key];
        }

        return $shuffledArray;
    }

    /**
     * Wrapper method for iterating through array of objects and converting
     *  them to strings
     *
     * @param array
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function toStrings(array $array)
    {
        // Validate
        if (!self::populated($array)) {
            throw new Hobis_Api_Exception(sprintf('Invalid array: (%s)', serialize($array)));
        }

        return array_map(create_function('$element', 'return (string) $element;'), $array);
    }
}
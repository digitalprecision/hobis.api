<?php

class Hobis_Api_Orm_Doctrine_Collection_Package
{
    /**
     * Wrapper method for converting an orm collection to specific separator (default,) based on column
     *
     * @param object
     * @param string
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function toString(array $options)
    {
        //-----
        // Validate
        //-----
        if ((false === Hobis_Api_Array_Package::populatedKey('collection', $options)) ||
            (false === is_callable(array($options['collection'], 'toArray')))) {
            throw new Hobis_Api_Exception(sprintf('Invalid collection: %s', serialize($options)));
        } elseif (false === Hobis_Api_Array_Package::populatedKey('columnName', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid columnName: %s', serialize($options)));
        }
        //-----

        $collection = $options['collection'];
        $columnName = $options['columnName'];
        $separator = (Hobis_Api_Array_Package::populatedKey('separator', $options)) ? $options['separator'] : ', ';

        foreach ($collection->toArray() as $element) {

            if (Hobis_Api_Array_Package::populatedKey($columnName, $element)) {
                $columnValues[] = $element[$columnName];
            }
        }

        return (isset($columnValues)) ? implode($separator, $columnValues) : null;
    }

    /**
     * Wrapper method for converting a collection object to flat array based on specified column_name
     *
     * @param array
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function toArray(array $options)
    {
        return Hobis_Api_Array_Package::castStrictTypes(array_map('trim', array_filter(explode(',', self::toString($options)))));
    }
}
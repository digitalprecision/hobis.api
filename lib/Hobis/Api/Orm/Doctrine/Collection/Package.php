<?php

class Hobis_Api_Orm_Doctrine_Collection_Package
{
    /**
     * Wrapper method for converting an orm collection to a csv based on column
     *
     * @param object
     * @param string
     * @return array
     * @throws Hobis_Api_Exception
     */
    //public static function toString(Doctrine_Collection $collection, $columnName)
    public static function toString(array $options)
    {
        //-----
        // Validate
        //-----
        if ((!Hobis_Api_Array_Package::populatedKey('collection', $options)) ||
            (!is_callable(array($options['collection'], 'toArray')))) {
            throw new Hobis_Api_Exception(sprintf('Invalid collection: %s', serialize($options)));
        } elseif (!Hobis_Api_Array_Package::populatedKey('columnName', $options)) {
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
}
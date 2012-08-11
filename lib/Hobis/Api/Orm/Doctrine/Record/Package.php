<?php

class Hobis_Api_Orm_Doctrine_Record_Package
{
    /**
     * Wrapper method for comparing two doctrine records
     *
     * @param Doctrine_Record
     * @param Doctrine_Record
     * @return bool
     */
    public static function compare(Doctrine_Record $object1, Doctrine_Record $object2)
    {
        // Note 1: toArray() is called with false, otherwise relations are loaded
        // Note 2: Had to pass array through castStrictTypes, there were some instances where ids
        //  were strings when they should have been ints
        $hash1 = md5(serialize(Hobis_Api_Array_Package::castStrictTypes($object1->toArray(false))));
        $hash2 = md5(serialize(Hobis_Api_Array_Package::castStrictTypes($object2->toArray(false))));

        return ($hash1 === $hash2) ? true : false;
    }
}
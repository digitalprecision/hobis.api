<?php

class HobisTest_Api_Module_Cache_PackageTest extends PHPUnit_Framework_TestCase
{
    //-----
    // Test methods
    //-----

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testDelete_exception_uninitializedArgs()
    {
        $cachedValue = Hobis_Api_Cache_Package::delete(new Hobis_Api_Cache_Key(), null);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testCacheFactory_exception_noArgs()
    {
        $cache = Hobis_Api_Cache_Package::factory(null);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testCacheFactory_exception_invalidType()
    {
        $cache = Hobis_Api_Cache_Package::factory('nonValidType');
    }

    public function testCacheFactory_base()
    {
        $cache = Hobis_Api_Cache_Package::factory(Hobis_Api_Cache::TYPE_VOLATILE);

        $this->assertInstanceOf('Hobis_Api_Cache', $cache);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testGet_exception_uninitializedArgs()
    {
        $cachedValue = Hobis_Api_Cache_Package::get(new Hobis_Api_Cache_Key(), null);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testSet_exception_uninitializedArgs()
    {
        Hobis_Api_Cache_Package::set(new Hobis_Api_Cache_Key(), null);
    }
    //-----
}
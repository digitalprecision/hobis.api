<?php

class HobisTest_Api_Module_Cache_PackageTest extends PHPUnit_Framework_TestCase
{
    //-----
    // Test methods
    //-----

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testDelete_exception_uninitializedArgs()
    {
        $cachedValue = CoreLib_Api_Cache_Package::delete(new CoreLib_Api_Cache_Key(), null);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testCacheFactory_exception_noArgs()
    {
        $cache = CoreLib_Api_Cache_Package::factory(null);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testCacheFactory_exception_invalidType()
    {
        $cache = CoreLib_Api_Cache_Package::factory('nonValidType');
    }

    public function testCacheFactory_base()
    {
        $cache = CoreLib_Api_Cache_Package::factory(CoreLib_Api_Cache::TYPE_VOLATILE);

        $this->assertInstanceOf('CoreLib_Api_Cache', $cache);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testGet_exception_uninitializedArgs()
    {
        $cachedValue = CoreLib_Api_Cache_Package::get(new CoreLib_Api_Cache_Key(), null);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testSet_exception_uninitializedArgs()
    {
        CoreLib_Api_Cache_Package::set(new CoreLib_Api_Cache_Key(), null);
    }
    //-----
}
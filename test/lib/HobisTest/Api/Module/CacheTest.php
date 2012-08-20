<?php

class HobisTest_Api_Module_CacheTest extends PHPUnit_Framework_TestCase
{
    const TEST_KEY_PREFIX   = 'test';
    const TEST_VALUE        = 'brown chicken, brown cow';
    const TEST_EXPIRY       = 30;

    //-----
    // Support methods
    //-----
    protected $cache;
    protected $randomNumber;

    protected function getCache()
    {
        return $this->cache;
    }

    protected function getKnownGoodKey()
    {
        return self::TEST_KEY_PREFIX . Hobis_Api_Cache_Key::SEPARATOR . $this->getRandomNumber();
    }

    protected function getRandomNumber()
    {
        return $this->randomNumber;
    }
    //-----

    //-----
    // Setup and teardown
    //-----
    public function setup()
    {
        $this->cache        = Hobis_Api_Cache_Package::factory(Hobis_Api_Cache::TYPE_VOLATILE);
        $this->randomNumber = mt_rand();
    }

    public function teardown()
    {
        unset(
            $this->cache,
            $this->randomNumber
        );
    }
    //-----

    //-----
    // Test methods
    //-----

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testDelete_exception_noArgs()
    {
        $this->getCache()->get(null);
    }

    public function testDelete_base()
    {
        $this->getCache()->set($this->getKnownGoodKey(), self::TEST_VALUE);

        $this->assertTrue($this->getCache()->delete($this->getKnownGoodKey()));

        $this->assertFalse($this->getCache()->get($this->getKnownGoodKey()));
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testGet_exception_noArgs()
    {
        $this->getCache()->get(null);
    }

    public function testGet_base()
    {
        $this->getCache()->set($this->getKnownGoodKey(), self::TEST_VALUE);

        $this->assertSame(self::TEST_VALUE, $this->getCache()->get($this->getKnownGoodKey()));
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testSet_exception_noArgs()
    {
        $this->getCache()->set(null, null);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testSet_exception_noKey()
    {
        $this->getCache()->set(null, self::TEST_VALUE);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testSet_exception_noValue()
    {
        $this->getCache()->set($this->getKnownGoodKey(), null);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testSet_exception_expiryInvalidChar()
    {
        $this->getCache()->set($this->getKnownGoodKey(), self::TEST_VALUE, 'a');
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testSet_exception_expiryInvalidNumber()
    {
        $this->getCache()->set($this->getKnownGoodKey(), self::TEST_VALUE, -1);
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testSet_exception_expiryTooLow()
    {
        $this->getCache()->set($this->getKnownGoodKey(), self::TEST_VALUE, 2);
    }

    public function testSet_base()
    {
        $this->getCache()->set($this->getKnownGoodKey(), self::TEST_VALUE);

        $this->assertSame(self::TEST_VALUE, $this->getCache()->get($this->getKnownGoodKey()));
    }
    //-----
}
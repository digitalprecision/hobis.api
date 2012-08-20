<?php

class HobisTest_Api_Flow_CacheTest extends PHPUnit_Framework_TestCase
{
    const TEST_EXPIRY       = 30;
    const TEST_KEY_PREFIX   = 'test';
    const TEST_VALUE        = 'brown chicken, brown cow';

    //-----
    // Support methods
    //-----
    protected $object;
    protected $randomNumber;

    protected function getKnownGoodKey()
    {
        return self::TEST_KEY_PREFIX . Hobis_Api_Cache_Key::SEPARATOR . $this->getRandomNumber() . Hobis_Api_Cache_Key::SEPARATOR . '1';
    }

    protected function getObject()
    {
        return $this->object;
    }

    protected function getRandomNumber()
    {
        return $this->randomNumber;
    }
    //-----

    //-----
    // Setup and teardown
    //-----
    public function setUp()
    {
        $this->object       = $this->getMock('Hobis_PhpUnit_DefaultTestObject');
        $this->randomNumber = mt_rand();
    }

    public function tearDown()
    {
        unset(
            $this->object,
            $this->randomNumber
        );
    }
    //-----

    //-----
    // Test methods
    //-----
    public function testCache()
    {
        // Configure object
        $this->object->expects($this->any())->method('getId')->will($this->returnValue(1));

        $key = Hobis_Api_Cache_Key_Package::factory(
            array(
                'dynamicSuffixes'   => array($this->getRandomNumber(), $this->getObject()->getId()),
                'expiry'            => self::TEST_EXPIRY,
                'staticPrefix'      => self::TEST_KEY_PREFIX,
                'value'             => self::TEST_VALUE
            )
        );

        $setStatus = Hobis_Api_Cache_Package::set($key);

        $this->assertTrue($setStatus);

        $cachedValue = Hobis_Api_Cache_Package::get($key);

        $this->assertSame($key->getKey(), $this->getKnownGoodKey());
        $this->assertSame($cachedValue, self::TEST_VALUE);
    }
    //-----
}
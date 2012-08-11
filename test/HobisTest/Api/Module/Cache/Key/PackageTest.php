<?php

class HobisTest_Api_Module_Cache_Key_PackageTest extends PHPUnit_Framework_TestCase
{
    const TEST_KEY_PREFIX   = 'test';
    const TEST_VALUE        = 'brown chicken, brown cow';

    //-----
    // Support methods
    //-----
    protected $randomNumber;

    protected function getRandomNumber()
    {
        return $this->randomNumber;
    }

    protected function getKnownGoodKey()
    {
        return self::TEST_KEY_PREFIX . CoreLib_Api_Cache_Key::SEPARATOR . $this->getRandomNumber() . CoreLib_Api_Cache_Key::SEPARATOR . '1';
    }
    //-----

    //-----
    // Setup and teardown
    //-----
    public function setUp()
    {
        $this->object       = $this->getMock('HobisTest_Lib_DefaultTestObject');
        $this->randomNumber = mt_rand();
    }

    public function tearDown()
    {
        unset($this->object, $this->randomNumber);
    }
    //-----

    //-----
    // Test methods
    //-----

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testGenerate_exception_noArgs()
    {
        $key = CoreLib_Api_Cache_Key_Package::generate(null);
    }

    public function testGenerate_base()
    {
        $this->assertSame(CoreLib_Api_Cache_Key_Package::generate(self::TEST_KEY_PREFIX, array($this->getRandomNumber(), 1)), $this->getKnownGoodKey());
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testFactory_exception_noArgs()
    {
        $key = CoreLib_Api_Cache_Key_Package::factory(array());
    }

    public function testFactory_base()
    {
        $key = CoreLib_Api_Cache_Key_Package::factory(
            array(
                'staticPrefix'  => self::TEST_KEY_PREFIX
            )
        );

        $this->assertInstanceOf('CoreLib_Api_Cache_Key', $key);

        $this->assertSame($key->getKey(), self::TEST_KEY_PREFIX);
    }

    public function testFactory_base_withDynamicSuffixes()
    {
        // Configure object
        $this->object->expects($this->any())->method('getId')->will($this->returnValue(1));

        $key = CoreLib_Api_Cache_Key_Package::factory(
            array(
                'dynamicSuffixes'   => array($this->getRandomNumber(), $this->object->getId()),
                'staticPrefix'      => self::TEST_KEY_PREFIX
            )
        );

        $this->assertInstanceOf('CoreLib_Api_Cache_Key', $key);

        $this->assertSame($key->getKey(), $this->getKnownGoodKey());
    }

    public function testFactory_base_withValue()
    {
        $key = CoreLib_Api_Cache_Key_Package::factory(
            array(
                'dynamicSuffixes'   => array($this->getRandomNumber(), $this->object->getId()),
                'staticPrefix'      => self::TEST_KEY_PREFIX,
                'value'             => self::TEST_VALUE
            )
        );

        $this->assertInstanceOf('CoreLib_Api_Cache_Key', $key);

        $this->assertSame($key->getValue(), self::TEST_VALUE);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testValidate_exception_noArgs()
    {
        CoreLib_Api_Cache_Key_Package::validate(null);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testValidate_exception_InvalidChar()
    {
        $knownBadKey = str_ireplace(CoreLib_Api_Cache_Key::SEPARATOR, '*', $this->getKnownGoodKey());

        CoreLib_Api_Cache_Key_Package::validate($knownBadKey);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testValidate_exception_withSpaces()
    {
        $knownBadKey = str_ireplace(CoreLib_Api_Cache_Key::SEPARATOR, ' ', $this->getKnownGoodKey());

        CoreLib_Api_Cache_Key_Package::validate($knownBadKey);
    }

    public function testValidate_base()
    {
        CoreLib_Api_Cache_Key_Package::validate($this->getKnownGoodKey());
    }
    //-----
}
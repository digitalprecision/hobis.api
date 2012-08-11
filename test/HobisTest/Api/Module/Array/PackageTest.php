<?php

class HobisTest_Api_Module_Array_PackageTest extends PHPUnit_Framework_TestCase
{
    //-----
    // Test methods
    //-----
    public function testAndify_base()
    {
        $this->assertSame(CoreLib_Api_Array_Package::andify(array('foo', 'bar')), 'foo and bar');
    }

    public function testOrify_base()
    {
        $this->assertSame(CoreLib_Api_Array_Package::orify(array('foo', 'bar')), 'foo or bar');
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testToList_exception_noArgs()
    {
        CoreLib_Api_Array_Package::toList(array());
    }

    public function testToList_base()
    {
        $this->assertSame(CoreLib_Api_Array_Package::toList(array('array' => array('foo'))), 'foo');
        $this->assertSame(CoreLib_Api_Array_Package::toList(array('array' => array('foo', 'bar'))), 'foo, bar');
        $this->assertSame(CoreLib_Api_Array_Package::toList(array('array' => array('foo', 'bar'), 'conjunction' => 'test')), 'foo test bar');
        $this->assertSame(CoreLib_Api_Array_Package::toList(array('array' => array('a', 'b', 'c'))), 'a, b, c');
        $this->assertSame(CoreLib_Api_Array_Package::toList(array('array' => array('a', 'b', 'c'), 'conjunction' => 'test')), 'a, b, test c');
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testImplodeWithQuotes_exception_noArgs()
    {
        CoreLib_Api_Array_Package::implodeWithQuotes(array());
    }

    public function testImplodeWithQuotes_base()
    {
        $this->assertSame(CoreLib_Api_Array_Package::implodeWithQuotes(array('foo', 'bar')), '\'foo\',\'bar\'');
    }

    public function testPopulated_base()
    {
        $this->assertTrue(CoreLib_Api_Array_Package::populated(array('foo')));
        $this->assertFalse(CoreLib_Api_Array_Package::populated(array()));
    }

    public function testPopulated_base_withSpecificValue()
    {
        $testArray = array('foo' => 'bar');

        $this->assertTrue(CoreLib_Api_Array_Package::populatedKey('foo', $testArray, 'bar', false));
    }

    public function testPopulated_base_withSpecificValueAndType()
    {
        $testArray = array('foo' => true);

        $this->assertTrue(CoreLib_Api_Array_Package::populatedKey('foo', $testArray, true));
        $this->assertFalse(CoreLib_Api_Array_Package::populatedKey('foo', $testArray, 'true'));
    }

    public function testPopulatedKey_base_withStandardArray()
    {
        $testArray = array('foo' => 'bar');

        $this->assertTrue(CoreLib_Api_Array_Package::populatedKey('foo', $testArray));
    }

    public function testPopulatedKey_base_withObject()
    {
        $testArray = array('foo' => new stdClass());

        $this->assertTrue(CoreLib_Api_Array_Package::populatedKey('foo', $testArray));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testShuffle_exception_noArgs()
    {
        CoreLib_Api_Array_Package::shuffle(array());
    }

    public function testShuffle_base()
    {
        $testArray = array(
            'beer'      => 'keystone light',
            'liqueur'   => 'jaigermeister',
            'band'      => 'metallica',
            'true'      => 'ford'
        );
        $shuffledTestArray  = CoreLib_Api_Array_Package::shuffle($testArray);

        $this->assertCount(4, $shuffledTestArray);

        foreach ($testArray as $key => $value) {
            $this->assertSame($testArray[$key], $shuffledTestArray[$key]);
        }
    }
    //-----
}

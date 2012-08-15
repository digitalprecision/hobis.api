<?php

class HobisTest_Api_Module_Array_PackageTest extends PHPUnit_Framework_TestCase
{
    //-----
    // Test methods
    //-----
    public function testAndify_base()
    {
        $this->assertSame(Hobis_Api_Array_Package::andify(array('foo', 'bar')), 'foo and bar');
    }

    public function testOrify_base()
    {
        $this->assertSame(Hobis_Api_Array_Package::orify(array('foo', 'bar')), 'foo or bar');
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testToList_exception_noArgs()
    {
        Hobis_Api_Array_Package::toList(array());
    }

    public function testToList_base()
    {
        $this->assertSame(Hobis_Api_Array_Package::toList(array('array' => array('foo'))), 'foo');
        $this->assertSame(Hobis_Api_Array_Package::toList(array('array' => array('foo', 'bar'))), 'foo, bar');
        $this->assertSame(Hobis_Api_Array_Package::toList(array('array' => array('foo', 'bar'), 'conjunction' => 'test')), 'foo test bar');
        $this->assertSame(Hobis_Api_Array_Package::toList(array('array' => array('a', 'b', 'c'))), 'a, b, c');
        $this->assertSame(Hobis_Api_Array_Package::toList(array('array' => array('a', 'b', 'c'), 'conjunction' => 'test')), 'a, b, test c');
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testImplodeWithQuotes_exception_noArgs()
    {
        Hobis_Api_Array_Package::implodeWithQuotes(array());
    }

    public function testImplodeWithQuotes_base()
    {
        $this->assertSame(Hobis_Api_Array_Package::implodeWithQuotes(array('foo', 'bar')), '\'foo\',\'bar\'');
    }

    public function testPopulated_base()
    {
        $this->assertTrue(Hobis_Api_Array_Package::populated(array('foo')));
        $this->assertFalse(Hobis_Api_Array_Package::populated(array()));
    }

    public function testPopulated_base_withSpecificValue()
    {
        $testArray = array('foo' => 'bar');

        $this->assertTrue(Hobis_Api_Array_Package::populatedKey('foo', $testArray, 'bar', false));
    }

    public function testPopulated_base_withSpecificValueAndType()
    {
        $testArray = array('foo' => true);

        $this->assertTrue(Hobis_Api_Array_Package::populatedKey('foo', $testArray, true));
        $this->assertFalse(Hobis_Api_Array_Package::populatedKey('foo', $testArray, 'true'));
    }

    public function testPopulatedKey_base_withStandardArray()
    {
        $testArray = array('foo' => 'bar');

        $this->assertTrue(Hobis_Api_Array_Package::populatedKey('foo', $testArray));
    }

    public function testPopulatedKey_base_withObject()
    {
        $testArray = array('foo' => new stdClass());

        $this->assertTrue(Hobis_Api_Array_Package::populatedKey('foo', $testArray));
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testShuffle_exception_noArgs()
    {
        Hobis_Api_Array_Package::shuffle(array());
    }

    public function testShuffle_base()
    {
        $testArray = array(
            'beer'      => 'keystone light',
            'liqueur'   => 'jaigermeister',
            'band'      => 'metallica',
            'true'      => 'ford'
        );
        $shuffledTestArray  = Hobis_Api_Array_Package::shuffle($testArray);

        $this->assertCount(4, $shuffledTestArray);

        foreach ($testArray as $key => $value) {
            $this->assertSame($testArray[$key], $shuffledTestArray[$key]);
        }
    }
    //-----
}

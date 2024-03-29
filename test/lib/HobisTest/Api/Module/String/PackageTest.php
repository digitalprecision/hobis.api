<?php

class HobisTest_Api_Module_String_PackageTest extends PHPUnit_Framework_TestCase
{
    //-----
    // Test methods
    //-----

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testFromArray_exception_noArgs()
    {
        Hobis_Api_String_Package::fromArray(array());
    }

    public function testFromArray_base()
    {
        $this->assertSame(Hobis_Api_String_Package::fromArray(array('a', 'b')), 'a_b');

        $this->assertSame(Hobis_Api_String_Package::fromArray(array('a', 'b'), '+'), 'a+b');
    }

    public function testPopulated_base()
    {
        $this->assertFalse(Hobis_Api_String_Package::populated(null));

        $this->assertTrue(Hobis_Api_String_Package::populated('test'));

        $this->assertFalse(Hobis_Api_String_Package::populated(array('foo' => 'bar')));

        //-----
        // Object test
        //-----
        $object = $this->getMock('Hobis_PhpUnit_DefaultTestObject');

        // Object hasn't been configured with __toString yet, so expect a fail
        $this->assertFalse(Hobis_Api_String_Package::populated($object));

        $object->expects($this->any())->method('__toString')->will($this->returnValue('Hobis_PhpUnit_DefaultTestObject'));

        // Object has been configured with __toString, so expect a pass
        $this->assertTrue(Hobis_Api_String_Package::populated($object));
        //-----
    }

    public function testPopulatedNumeric_base()
    {
        $this->assertFalse(Hobis_Api_String_Package::populatedNumeric(null));
        $this->assertFalse(Hobis_Api_String_Package::populatedNumeric('test'));
        $this->assertTrue(Hobis_Api_String_Package::populatedNumeric(1));
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testTokenize_exception_noArgs()
    {
        Hobis_Api_String_Package::tokenize(array());
    }

    /**
     * @expectedException Hobis_Api_Exception
     */
    public function testTokenize_exception_InvalidChar()
    {
        Hobis_Api_String_Package::tokenize(array('value' => '[This is my test string]'));
    }

    public function testTokenize_base()
    {
        // Default tokenize
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => 'a b c d')), 'a-b-c-d');

        // Custom separator
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => 'a b c d', 'separator' => '+')), 'a+b+c+d');

        // Custom chars to remove
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => 'a b c d', 'charsToRemove' => array('a', 'c'))), '-b--d');

        // Allowed chars
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => '$a #b c^ d', 'allowedChars' => array('$', '#', '^'))), '$a-#b-c^-d');

        // Remove quotes
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => 'a "b" \'c\' d')), 'a-b-c-d');

        // Html entity decode
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => 'a <b>b</b> c <i>d</i>')), 'a--b-b--b--c--i-d--i-');

        // Alpha chars only
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => '$a #b c^ d')), '-a--b-c--d');

        // Lower case
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => 'A B c D')), 'a-b-c-d');

        // Urlencode
        $this->assertSame(Hobis_Api_String_Package::tokenize(array('value' => 'a=alpha&b=beta&c=charlie&d=delta', 'allowedChars' => array('&', '='), 'urlEncode' => true)), 'a%3Dalpha%26b%3Dbeta%26c%3Dcharlie%26d%3Ddelta');
    }
    //-----
}
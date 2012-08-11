<?php

class HobisTest_Api_Module_ExceptionTest extends PHPUnit_Framework_TestCase
{
    //-----
    // Test methods
    //-----
    public function testToString_base()
    {
        try {
            throw new CoreLib_Api_Exception('Test message');
        } catch (Exception $e) {
            $this->assertSame((string) $e, sprintf('%s (%s): %s', $e->getFile(), $e->getLine(), $e->getMessage()));
        }
    }
    //-----
}
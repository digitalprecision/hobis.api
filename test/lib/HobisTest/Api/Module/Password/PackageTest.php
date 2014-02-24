<?php

class HobisTest_Api_Module_Password_PackageTest extends PHPUnit_Framework_TestCase
{
    const PASSWORD = 'andboomgoesthedynomite!';
    
    //-----
    // Support methods
    //-----
    protected function getKnownGoodHash()
    {
        return 'sha512:4790:B+OYE4SLHtgJmaEcc14fJLv22pFk2Cq2:Pal/+XmHb9BG8v6hSN/K3djsru6Sn57c';
    }
    //-----
    
    //-----
    // Test methods
    //-----
    public function testCreateHash_base()
    {
        $hash = Hobis_Api_Password_Package::generateHash(self::PASSWORD);
        
        $hashParts = explode(':', $hash);
        
        $this->assertCount(Hobis_Api_Password_Package::HASH_SECTION_COUNT, $hashParts);
    }
    
    public function testValidate_base()
    {
        $status = Hobis_Api_Password_Package::validate(self::PASSWORD, $this->getKnownGoodHash());
        
        $this->assertSame(true, $status);
    }
    //-----
}
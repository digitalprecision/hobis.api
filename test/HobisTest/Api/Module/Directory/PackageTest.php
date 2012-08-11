<?php

class HobisTest_Api_Module_Directory_PackageTest extends PHPUnit_Framework_TestCase
{
    //-----
    // Support methods
    //-----

    protected $randomDir;
    protected $testDirs;
    protected $testPath;
    protected $timestamp;
    protected $timestampTemplate;

    public function getRandomDir()
    {
        return $this->randomDir;
    }

    public function getTestDirs()
    {
        return $this->testDirs;
    }

    public function getTestPath()
    {
        return $this->testPath;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getTimestampTemplate()
    {
        return $this->timestampTemplate;
    }
    //-----

    //-----
    // Setup and teardown
    //-----
    public function setup()
    {
        $this->randomDir            = md5(mt_rand());
        $this->testPath             = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'unit_test' . DIRECTORY_SEPARATOR . 'directory' . DIRECTORY_SEPARATOR . $this->randomDir;
        $this->timestamp            = time();
        $this->timestampTemplate    = DIRECTORY_SEPARATOR . 'Y' . DIRECTORY_SEPARATOR . 'm' . DIRECTORY_SEPARATOR . 'd' . DIRECTORY_SEPARATOR . 'h' . DIRECTORY_SEPARATOR . 'i' . DIRECTORY_SEPARATOR . 's';

        $this->testDirs = array(
            'tmp',
            'unit_test',
            'directory',
            $this->randomDir
        );
    }

    public function teardown()
    {
        unset(
            $this->randomDir,
            $this->testPath,
            $this->timestamp,
            $this->timestampTemplate,
            $this->testDirs
        );
    }
    //-----

    //-----
    // Test methods
    //-----

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testFromArray_exception_noArgs()
    {
        CoreLib_Api_Directory_Package::fromArray(array());
    }

    public function testFromArray_base()
    {
        $this->assertSame(CoreLib_Api_Directory_Package::fromArray(
            array(
                'tmp',
                'unit_test',
                'directory',
                $this->getRandomDir()
            )
        ), $this->getTestPath());
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testFromId_exception_withZeroArg()
    {
        CoreLib_Api_Directory_Package::fromId(0);
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testFromId_exception_withAlphaArg()
    {
        CoreLib_Api_Directory_Package::fromId('q');
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testFromId_exception_withNegativeArg()
    {
        CoreLib_Api_Directory_Package::fromId(-1);
    }

    public function testFromId_base()
    {
        // /1
        $this->assertSame(CoreLib_Api_Directory_Package::fromId(1), DIRECTORY_SEPARATOR . '1');

        // /10/0
        $this->assertSame(CoreLib_Api_Directory_Package::fromId(100), DIRECTORY_SEPARATOR . '10' . DIRECTORY_SEPARATOR . '0');

        // /99/99
        $this->assertSame(CoreLib_Api_Directory_Package::fromId(9999), DIRECTORY_SEPARATOR . '99' . DIRECTORY_SEPARATOR . '99');

        // /10/10/0
        $this->assertSame(CoreLib_Api_Directory_Package::fromId(10000), DIRECTORY_SEPARATOR . '10' . DIRECTORY_SEPARATOR . '00' . DIRECTORY_SEPARATOR . '0');

        // /10/10/9
        $this->assertSame(CoreLib_Api_Directory_Package::fromId(10009), DIRECTORY_SEPARATOR . '10' . DIRECTORY_SEPARATOR . '00' . DIRECTORY_SEPARATOR . '9');

        // /10/10/9
        $this->assertSame(CoreLib_Api_Directory_Package::fromId(10010), DIRECTORY_SEPARATOR . '10' . DIRECTORY_SEPARATOR . '01' . DIRECTORY_SEPARATOR . '0');
    }

    public function testGetFormattedTimestamp_base()
    {
        $this->assertSame(CoreLib_Api_Directory_Package::getFormattedTimestamp($this->getTimestamp()), date($this->getTimestampTemplate(), $this->getTimestamp()));
    }

    public function testGetTimestampTemplate_base()
    {
        $this->assertSame(CoreLib_Api_Directory_Package::getTimestampTemplate(), $this->getTimestampTemplate());
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testMake_exception_noArgs()
    {
        CoreLib_Api_Directory_Package::make(array());
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testMake_exception_dirIsAFile()
    {
        $testFileUri = $this->getTestPath() . DIRECTORY_SEPARATOR . 'test.txt';

        mkdir($this->getTestPath(), CoreLib_Api_Filesystem::PERMS_RWX__RWX__R_X, true);

        file_put_contents($testFileUri, 'Testing.');

        CoreLib_Api_Directory_Package::make(array('dir' => $testFileUri));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testMake_exception_unableToCreateNewDir()
    {
        $testPath = $this->getTestPath() . DIRECTORY_SEPARATOR . md5(rand());

        CoreLib_Api_Directory_Package::make(array('dir' => $this->getTestPath(), 'perms' => CoreLib_Api_Filesystem::PERMS_R__R__R));

        CoreLib_Api_Directory_Package::make(array('dir' => $testPath));
    }

    public function testMake_exception_newDirIsNotWritable()
    {
        $testPath = $this->getTestPath() . DIRECTORY_SEPARATOR . md5(rand());

        CoreLib_Api_Directory_Package::make(array('dir' => $this->getTestPath(), 'perms' => CoreLib_Api_Filesystem::PERMS_R_X__R_X__E));

        $this->assertFalse(is_writable($testPath));
    }

    public function testMake_base()
    {
        CoreLib_Api_Directory_Package::make(array('dir' => $this->getTestPath()));

        $this->assertTrue(is_writable($this->getTestPath()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testRemove_exception_invalidBaseDir()
    {
        CoreLib_Api_Directory_Package::remove(null, 'tmp');
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testRemove_Exception_invalidDirToRemove()
    {
        CoreLib_Api_Directory_Package::remove('tmp', null);
    }

    public function testRemove_base()
    {
        $dirToRemove = $this->getTestPath() . DIRECTORY_SEPARATOR . md5(rand());

        try {
            CoreLib_Api_Directory_Package::remove($this->getTestPath(), $dirToRemove);
        } catch (Exception $e) {
            if ($e->getCode() !== CoreLib_Api_Exception::CODE_DIR_BASE_EQUALS_REMOVE) {
                throw $e;
            }
        }

        $this->assertFalse(is_dir($dirToRemove));
    }

    /**
     *@expectedException CoreLib_Api_Exception
     */
    public function testToArray_exeption_noArgs()
    {
        CoreLib_Api_Directory_Package::toArray(null);
    }

    public function testToArray_base()
    {
        $this->assertEquals(CoreLib_Api_Directory_Package::toArray($this->getTestPath()), $this->getTestDirs());
    }
    //-----
}
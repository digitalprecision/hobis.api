<?php

class HobisTest_Api_Module_File_PackageTest extends PHPUnit_Framework_TestCase
{
    const TEST_CONTENT = 'I am a l33t testing string for teh testings!';

    //-----
    // Support methods
    //-----
    protected $destFileName;
    protected $destFileUri;
    protected $dummySourceFileUri;
    protected $randomDir;
    protected $sourceFileName;
    protected $sourceFileUri;
    protected $testPath;
    protected $testBasePath;

    public function getDestFileName()
    {
        return $this->destFileName;
    }

    public function getDestFileUri()
    {
        return $this->destFileUri;
    }

    public function getDummySourceFileUri()
    {
        return $this->dummySourceFileUri;
    }

    public function getRandomDir()
    {
        return $this->randomDir;
    }

    public function getSourceFileName()
    {
        return $this->sourceFileName;
    }

    public function getSourceFileUri()
    {
        return $this->sourceFileUri;
    }

    public function getTestPath()
    {
        return $this->testPath;
    }

    public function getTestBasePath()
    {
        return $this->testBasePath;
    }

    public function touchFile($fileUri)
    {
        mkdir($this->getTestPath(), CoreLib_Api_Filesystem::PERMS_RWX__RWX__R_X, true);

        if (!@is_dir($this->getTestPath())) {
            throw new PHPUnit_Framework_Exception(sprintf('Invalid testPath: (%s)', $this->getTestPath()));
        }

        touch($fileUri);

        if (!@is_file($fileUri)) {
            throw new PHPUnit_Framework_Exception(sprintf('Invalid sourceFileUri: (%s)', $fileUri));
        }
    }
    //-----

    //-----
    // Setup and teardown
    //-----
    public function setUp()
    {
        $this->destFileName     = 'destFile' . substr(md5(mt_rand()), 0, 10) . '.txt';
        $this->randomDir        = md5(mt_rand());
        $this->sourceFileName   = 'sourceFile_' . substr(md5(mt_rand()), 0, 10) . '.txt';

        $this->testPath     = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'unit_test' . DIRECTORY_SEPARATOR . 'file' . DIRECTORY_SEPARATOR . $this->randomDir;
        $this->testBasePath = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'unit_test' . DIRECTORY_SEPARATOR . 'file';

        $this->destFileUri      = $this->testPath . DIRECTORY_SEPARATOR . $this->destFileName;
        $this->sourceFileUri    = $this->testPath . DIRECTORY_SEPARATOR . $this->sourceFileName;

        $this->dummySourceFileUri = DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR . 'null' . DIRECTORY_SEPARATOR . $this->sourceFileName;
    }

    public function tearDown()
    {
        unset(
            $this->destFileUri,
            $this->destFileName,
            $this->dummySourceFileUri,
            $this->randomDir,
            $this->sourceFileName,
            $this->sourceFileUri,
            $this->testPath,
            $this->testBasePath
        );
    }
    //-----

    //-----
    // Test methods
    //-----

    //---
    // Copy
    //---

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testCopy_exception_noDestFileUri()
    {
        CoreLib_Api_File_Package::copy(array('sourceFileUri' => $this->getSourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testCopy_exception_noSourceFileUri()
    {
        CoreLib_Api_File_Package::copy(array('destFileUri' => $this->getDestFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testCopy_exception_invalidSourceFile()
    {
        CoreLib_Api_File_Package::copy(array('destFileUri' => $this->getDestFileUri(), 'sourceFileUri' => $this->getSourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testCopy_exception_unableToCopy()
    {
        $this->touchFile($this->getSourceFileUri());

        chmod($this->getSourceFileUri(), 0000);

        CoreLib_Api_File_Package::copy(array('destFileUri' => $this->getDestFileUri(), 'sourceFileUri' => $this->getSourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testCopy_exception_unableToChmod()
    {
        $this->touchFile($this->getSourceFileUri());

        CoreLib_Api_File_Package::copy(array('destFileUri' => $this->getDestFileUri(), 'sourceFileUri' => $this->getSourceFileUri(), 'destFilePerms' => 'z'));
    }

    public function testCopy_base()
    {
        $this->touchFile($this->getSourceFileUri());

        CoreLib_Api_File_Package::copy(array('destFileUri' => $this->getDestFileUri(), 'sourceFileUri' => $this->getSourceFileUri()));

        $this->assertTrue(is_file($this->getDestFileUri()));
    }

    public function testCopy_base_customPerms()
    {
        $this->touchFile($this->getSourceFileUri());

        CoreLib_Api_File_Package::copy(array('destFileUri' => $this->getDestFileUri(), 'sourceFileUri' => $this->getSourceFileUri(), 'destFilePerms' => CoreLib_Api_Filesystem::PERMS_RWX__E__E));

        $filePerms = substr(sprintf('%o', fileperms($this->getDestFileUri())), -3);

        $this->assertSame(decoct(CoreLib_Api_Filesystem::PERMS_RWX__E__E), $filePerms);
    }
    //-----

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testGetBaseName_exception_noArgs()
    {
        CoreLib_Api_File_Package::getBaseName(null);
    }

    public function testGetBaseName_base()
    {
        $this->touchFile($this->getSourceFileUri());

        $this->assertSame(pathinfo($this->getSourceFileUri(), PATHINFO_BASENAME), CoreLib_Api_File_Package::getBaseName($this->getSourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testGetExtension_exception_noArgs()
    {
        CoreLib_Api_File_Package::getExtension(null);
    }

    public function testGetExtension_base()
    {
        $this->touchFile($this->getSourceFileUri());

        $this->assertSame(pathinfo($this->getSourceFileUri(), PATHINFO_EXTENSION), CoreLib_Api_File_Package::getExtension($this->getSourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testGetName_exception_noArgs()
    {
        CoreLib_Api_File_Package::getName(null);
    }

    public function testGetName_base()
    {
        $this->touchFile($this->getSourceFileUri());

        $this->assertSame(pathinfo($this->getSourceFileUri(), PATHINFO_FILENAME), CoreLib_Api_File_Package::getName($this->getSourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testIsFile_exception_noArgs()
    {
        CoreLib_Api_File_Package::isFile(null);
    }

    public function testIsFile_base()
    {
        $this->touchFile($this->getSourceFileUri());

        $this->assertTrue(CoreLib_Api_File_Package::isFile($this->getSourceFileUri()));
    }

    //---
    // Remove
    //---

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testRemove_exception_noArgs()
    {
        CoreLib_Api_File_Package::remove(array());
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testRemove_exception_removeDirInvalidBaseDir()
    {
        CoreLib_Api_File_Package::remove(array('removeDir' => true));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testRemove_exception_invalidFile()
    {
        CoreLib_Api_File_Package::remove(array('fileUri' => $this->getSourceFileUri()));
    }

    public function testRemove_base()
    {
        $this->touchFile($this->getSourceFileUri());

        CoreLib_Api_File_Package::remove(array('fileUri' => $this->getSourceFileUri()));

        $this->assertFalse(is_file($this->getSourceFileUri()));
    }

    public function testRemove_base_removeFileAndContainerDirs()
    {
        $this->touchFile($this->getSourceFileUri());

        try {
            CoreLib_Api_File_Package::remove(array('fileUri' => $this->getSourceFileUri(), 'removeDir' => true, 'baseDir' => $this->getTestBasePath()));
        } catch (Exception $e) {

            if ($e->getCode() !== CoreLib_Api_Exception::CODE_DIR_BASE_EQUALS_REMOVE) {
                throw $e;
            }

            $e = null;
        }

        $this->assertFalse(is_file($this->getSourceFileUri()));

        $this->assertFalse(is_dir($this->getTestPath()));

        $this->assertTrue(is_dir($this->getTestBasePath()));
    }
    //-----

    //---
    // Touch
    //---

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testTouch_exception_noArgs()
    {
        CoreLib_Api_File_Package::touch(array());
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testTouch_exception_fileAlreadyExists()
    {
        $this->touchFile($this->getSourceFileUri());

        CoreLib_Api_File_Package::touch(array('fileUri' => $this->getSourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testTouch_exception_unableToCreatePath()
    {
        CoreLib_Api_File_Package::touch(array('fileUri' => $this->getDummySourceFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testTouch_exception_unableToTouch()
    {
        CoreLib_Api_File_Package::touch(array('fileUri' => $this->getSourceFileUri(), 'timestamp' => 'z'));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testTouch_exception_unableToChmod()
    {
        CoreLib_Api_File_Package::touch(array('fileUri' => $this->getSourceFileUri(), 'filePerms' => 'z'));
    }

    public function testTouch_base()
    {
        CoreLib_Api_File_Package::touch(array('fileUri' => $this->getSourceFileUri()));

        $this->assertTrue(is_file($this->getSourceFileUri()));
    }

    public function testTouch_base_withTimestamp()
    {
        $timestamp = time();

        CoreLib_Api_File_Package::touch(array('fileUri' => $this->getSourceFileUri(), 'timestamp' => $timestamp));

        clearstatcache();

        $this->assertSame(filectime($this->getSourceFileUri()), $timestamp);

        clearstatcache();

        $this->assertSame(fileatime($this->getSourceFileUri()), $timestamp);
    }
    //---

    //---
    // Write
    //---

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testWrite_exception_noArgs()
    {
        CoreLib_Api_File_Package::write(array());
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testWrite_exception_invalidFileUri()
    {
        CoreLib_Api_File_Package::write(array('content' => self::TEST_CONTENT));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testWrite_exception_invalidContent()
    {
        CoreLib_Api_File_Package::write(array('fileUri' => $this->getDestFileUri()));
    }

    /**
     * @expectedException CoreLib_Api_Exception
     */
    public function testWrite_exception_unableToSecureLock()
    {
        $this->touchFile($this->getDestFileUri());

        $file = new SplFileObject($this->getDestFileUri(), CoreLib_Api_File::MODE_CREATE);

        $file->flock(LOCK_EX);

        CoreLib_Api_File_Package::write(array('fileUri' => $this->getDestFileUri(), 'content' => self::TEST_CONTENT));

        $file->flock(LOCK_UN);
    }

    public function testWrite_base()
    {
        $this->touchFile($this->getSourceFileUri());

        $file = new SplFileObject($this->getSourceFileUri(), CoreLib_Api_File::MODE_CREATE);

        $file->fwrite(self::TEST_CONTENT);

        CoreLib_Api_File_Package::write(array('fileUri' => $this->getDestFileUri(), 'content' => self::TEST_CONTENT));

        $this->assertSame($file->getSize(), filesize($this->getDestFileUri()));
    }
    //---
    //-----
}
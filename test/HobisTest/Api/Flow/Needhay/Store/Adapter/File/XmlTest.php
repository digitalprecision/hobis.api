<?php

class HobisTest_Api_Flow_Needhay_Store_Adapter_File_XmlTest extends HobisTest_Api_Flow_NeedhayTest
{
    //-----
    // Setup and teardown
    //-----
    public function setUp()
    {
        parent::setup();

        $this->needleStore->setAdapterType(Hobis_Api_Needhay_Store_Adapter::TYPE_FILE_XML);

        $this->baselineNeedleUris = array(
            'oneImage'          => substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/Store/Adapter/File/Xml/oneImage.xml',
            'twoImage'          => substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/Store/Adapter/File/Xml/twoImage.xml',
            'oneDescription'    => substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/Store/Adapter/File/Xml/oneDescription.xml',
            'oneOfAll'          => substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/Store/Adapter/File/Xml/oneOfAll.xml'
        );
    }

    public function tearDown()
    {
        parent::teardown();
    }
    //-----

    //-----
    // Test methods
    //-----
    public function testAssets()
    {
        $this->assertFileExists($this->sourceImages[0]);
        $this->assertFileExists($this->sourceImages[1]);

        foreach ($this->baselineNeedleUris as $baselineNeedleUri) {
            $this->assertFileExists($baselineNeedleUri);
        }
    }

    public function testOneImage()
    {
        $sourceImageUri = $this->sourceImages[0];

        //-----
        // Write file
        //-----
        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE);

        $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

        $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_ADD);
        $pointerCollection->setId(0);

        $pointer = new Hobis_Api_Needhay_Type_Image_Pointer();

        $pointer->setSizeCode(Hobis_Api_Image::SIZE_CODE_ORIGINAL);
        $pointer->setAssetName(Hobis_Api_File_Package::getBaseName($sourceImageUri));
        $pointer->setAssetContent($sourceImageUri);

        $pointerCollection->setPointer($pointer);

        $needleCollection->setPointerCollection($pointerCollection);

        $this->needle->setNeedleCollection($needleCollection);

        $this->needleStore->write($this->needle);
        //-----

        // Validate needle file
        $this->assertXmlFileEqualsXmlFile($this->baselineNeedleUris['oneImage'], $this->needleStore->getNeedleUri());

        // Validate haystack file
        $this->assertFileExists($pointer->getHaystackUri($this->needleStore));

        // Read file
        $assetFileUri = $this->needleStore->read()->getNeedleCollection(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE)->getPointerCollection(0)->getPointer(0)->getHaystackUri($this->needleStore);

        // Validate asset file
        $this->assertFileExists($assetFileUri);
    }

    public function testTwoImage()
    {
        //-----
        // Write file
        //-----
        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE);

        for ($i = 0 ; $i <= 1; $i++) {

            $sourceImageUri = $this->sourceImages[$i];

            $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

            $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_ADD);
            $pointerCollection->setId($i);

            $pointer = new Hobis_Api_Needhay_Type_Image_Pointer();

            $pointer->setSizeCode(Hobis_Api_Image::SIZE_CODE_ORIGINAL);
            $pointer->setAssetName(Hobis_Api_File_Package::getBaseName($sourceImageUri));
            $pointer->setAssetContent($sourceImageUri);

            $pointerCollection->setPointer($pointer);

            $needleCollection->setPointerCollection($pointerCollection);
        }

        $this->needle->setNeedleCollection($needleCollection);

        $this->needleStore->write($this->needle);
        //-----

        // Validate needle file
        $this->assertXmlFileEqualsXmlFile($this->baselineNeedleUris['twoImage'], $this->needleStore->getNeedleUri());

        // Validate haystack files
        foreach ($needleCollection->getPointerCollections() as $pointerCollection) {
            foreach ($pointerCollection->getPointers() as $pointer) {
                $this->assertFileExists($pointer->getHaystackUri($this->needleStore));
            }
        }

        //-----
        // Read file
        //-----
        $needle = $this->needleStore->read();

        foreach ($needle->getNeedleCollection(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE)->getPointerCollections() as $pointerCollection) {
            foreach ($pointerCollection->getPointers() as $pointer) {
                $assetFileUri = $this->haystackPath . DIRECTORY_SEPARATOR . $pointer->getAssetName();
                $this->assertFileExists($assetFileUri);
            }
        }
        //-----

        //-----
        // Remove an image
        //-----
        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE);

        $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

        $pointerCollection->setId(1);
        $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_REMOVE);

        $needleCollection->setPointerCollection($pointerCollection);

        // DO NOT USE $this->needle
        // We want to recycle the needle which was just read from, use it instead
        $needle->setNeedleCollection($needleCollection);

        $this->needleStore->write($needle);

        // Validate needle file
        $this->assertXmlFileEqualsXmlFile($this->baselineNeedleUris['oneImage'], $this->needleStore->getNeedleUri());

        // Validate haystack file
        $this->assertFalse(is_file($pointer->getHaystackUri($this->needleStore)));
        //-----
    }

    public function testOneDescription()
    {
        //-----
        // Write file
        //-----
        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION);

        $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

        $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_ADD);
        $pointerCollection->setId(0);

        $pointer = new Hobis_Api_Needhay_Type_Description_Pointer();

        $pointer->setAssetName(Hobis_Api_Needhay_Type_Description::ASSET_NAME);
        $pointer->setAssetContent($this->descriptions);

        $pointerCollection->setPointer($pointer);

        $needleCollection->setPointerCollection($pointerCollection);

        $this->needle->setNeedleCollection($needleCollection);

        $this->needleStore->write($this->needle);
        //-----

        // Validate needle file
        $this->assertXmlFileEqualsXmlFile($this->baselineNeedleUris['oneDescription'], $this->needleStore->getNeedleUri());

        // Validate haystack file
        $this->assertFileExists($pointer->getHaystackUri($this->needleStore));

        //-----
        // Read file
        //-----
        $assetFileUri = $this->needleStore->read()->getNeedleCollection(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION)->getPointerCollection(0)->getPointer(0)->getHaystackUri($this->needleStore);
        //-----

        // Validate asset file
        $this->assertFileExists($assetFileUri);
    }

    public function testOneOfAll()
    {
        //-----
        // Description
        //-----
        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION);

        $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

        $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_ADD);
        $pointerCollection->setId(0);

        $pointer = new Hobis_Api_Needhay_Type_Description_Pointer();

        $pointer->setAssetName(Hobis_Api_Needhay_Type_Description::ASSET_NAME);
        $pointer->setAssetContent($this->descriptions);

        $pointerCollection->setPointer($pointer);

        $needleCollection->setPointerCollection($pointerCollection);

        $this->needle->setNeedleCollection($needleCollection);
        //-----

        //-----
        // Image
        //-----
        $sourceImageUri = $this->sourceImages[0];

        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE);

        $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

        $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_ADD);
        $pointerCollection->setId(0);

        $pointer = new Hobis_Api_Needhay_Type_Image_Pointer();

        $pointer->setSizeCode(Hobis_Api_Image::SIZE_CODE_ORIGINAL);
        $pointer->setAssetName(Hobis_Api_File_Package::getBaseName($sourceImageUri));
        $pointer->setAssetContent($sourceImageUri);

        $pointerCollection->setPointer($pointer);

        $needleCollection->setPointerCollection($pointerCollection);

        $this->needle->setNeedleCollection($needleCollection);
        //-----

        // Write it
        $this->needleStore->write($this->needle);

        // Validate needle file
        $this->assertXmlFileEqualsXmlFile($this->baselineNeedleUris['oneOfAll'], $this->needleStore->getNeedleUri());

        // Read file
        $needle = $this->needleStore->read();

        // Validate haystack files
        $this->assertFileExists($needle->getNeedleCollection(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION)->getPointerCollection(0)->getPointer(0)->getHaystackUri($this->needleStore));
        $this->assertFileExists($needle->getNeedleCollection(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE)->getPointerCollection(0)->getPointer(0)->getHaystackUri($this->needleStore));

        //-----
        // Remove description collection
        //-----
        $needle = new Hobis_Api_Needhay_Needle();

        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION);

        $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

        $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_REMOVE);
        $pointerCollection->setId(0);

        $needleCollection->setPointerCollection($pointerCollection);

        $needle->setNeedleCollection($needleCollection);
        //-----

        // Write it
        $this->needleStore->write($needle);

        $this->assertXmlFileEqualsXmlFile($this->baselineNeedleUris['oneImage'], $this->needleStore->getNeedleUri());

        // Validate asset files
        $this->assertFileExists($this->needle->getNeedleCollection(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE)->getPointerCollection(0)->getPointer(0)->getHaystackUri($this->needleStore));
        $this->assertFalse(is_file($this->needle->getNeedleCollection(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION)->getPointerCollection(0)->getPointer(0)->getHaystackUri($this->needleStore)));
    }
    //-----
}
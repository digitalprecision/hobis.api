<?php

require_once 'ThumbLib.inc.php';

class Hobis_Api_Image_Package
{
    /**
     * Wrapper method for getting dimensions of an image to be squished inside a container
     * 
     * @param array $options
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function getSquishedDimensions(array $options)
    {	
        //-----
        // Validate
        //-----	
        if (!Hobis_Api_Array_Package::populatedKey('containerWidth', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[containerWidth]');
        } elseif (!Hobis_Api_Array_Package::populatedKey('containerHeight', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[containerHeight]');
        } elseif (!Hobis_Api_Array_Package::populatedKey('imageWidth', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[imageWidth]');
        } elseif (!Hobis_Api_Array_Package::populatedKey('imageHeight', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[imageHeight]');
        } elseif (!Hobis_Api_Array_Package::populatedKey('anchorDimension', $options)) {
            throw new Hobis_Api_Exception('Invalid $options[anchorDimension]');
        }
        //-----

        //-----
        // Localize
        //-----
        $containerWidth		= $options['containerWidth'];
        $containerHeight	= $options['containerHeight'];
        $imageWidth			= $options['imageWidth'];
        $imageHeight		= $options['imageHeight'];
        $anchorDimension	= $options['anchorDimension'];
        //-----

        switch ($options['anchorDimension']) {

            case Hobis_Api_Image::WIDTH:

                $resizeFactor   = ($containerWidth / $imageWidth);
                $width		= $containerWidth;
                $height		= floor($imageHeight * $resizeFactor);

                if ($height > $containerHeight) {

                    list($width, $height) = self::getSquishedDimensions(
                        array(
                            'containerWidth'	=> $containerWidth -=2,
                            'containerHeight'	=> $containerHeight,
                            'imageWidth'	=> $imageWidth,
                            'imageHeight'	=> $imageHeight,
                            'anchorDimension'	=> $anchorDimension
                        )
                    );					
                }

                break;

                    case Hobis_Api_Image::HEIGHT:

                            $resizeFactor   = ($containerHeight / $imageHeight);
                    $height         = $containerHeight;
                    $width			= floor($imageWidth * $resizeFactor);

                            if ($width > $containerWidth) {

                                    list($width, $height) = self::getSquishedDimensions(
                                            array(
                                                    'containerWidth'	=> $containerWidth,
                                                    'containerHeight'	=> $containerHeight -=2,
                                                    'imageWidth'		=> $imageWidth,
                                                    'imageHeight'		=> $imageHeight,
                                                    'anchorDimension'	=> $anchorDimension
                                            )
                                    );					
                            }

                            break;
            }

            return array($width, $height);
    }

    /**
        * Wrapper method for generating base read dir
        * 
        * @return string
        */
    public static function generateBaseDir()
    {
            return Hobis_Api_Directory_Package::fromArray(
                    array(
                            'home',
                            'files',
                            'app',
                            'upload'
                    )
            );
    }

/**
    * Wrapper method for retrieving images from store
    *  This method will also filter found images based on extra elements in $options array
    *
    * @param array $options
    * @return array
    * @throws Hobis_Api_Exception
    */
public static function getImages(array $options)
{
    //-----
    // Validate
    //-----
    if (!Hobis_Api_Array_Package::populatedKey('id', $options)) {
        throw new Hobis_Api_Exception('Invalid $options[id]');
    } elseif (!Hobis_Api_Array_Package::populatedKey('nsContext', $options)) {
        throw new Hobis_Api_Exception('Invalid $options[nsContext]');
    } elseif (!Hobis_Api_Array_Package::populatedKey('nsObject', $options)) {
        throw new Hobis_Api_Exception('Invalid $options[nsObject]');
    }
    //-----
    
    return array();

    // Init
    $filteredPointers = array();

    $needleStore = new Hobis_Api_Needle_Store();

    $needleStore->setNsContext($options['nsContext']);
    $needleStore->setId($options['id']);
    $needleStore->setNsObject($options['nsObject']);
    $needleStore->setNeedleType(Hobis_Api_Needle::TYPE_IMAGE);
    $needleStore->setStoreType(Hobis_Api_Needle_Store::TYPE_FILE_XML);

    try {			
        $needle = $needleStore->getReader($options)->read();
    } catch (Exception $e) {
            Hobis_Api_Log_Package::toErrorLog()->debug($e);
                    $e = null;
                    $needle  = null;
    }

    if ((!($needle instanceof Hobis_Api_Needle)) ||
        (!is_callable(array($needle, 'getCollections')))) {
        return array();
    }

    foreach ($needle->getCollections() as $collection) {

        // Specific multiple flags
        if ((Hobis_Api_Array_Package::populatedKey('imageId', $options)) &&
            (Hobis_Api_Array_Package::populatedKey('sizeCode', $options))) {

            // Must match on image id
            if ($collection->getId() !== $options['imageId']) {
                continue;
            }

            // Look for specific sizecode
            foreach ($collection->getPointers() as $pointer) {

                if ($pointer->getSizeCode() === $options['sizeCode']) {
                    $filteredPointers[] = $pointer;
                    break;
                }
            }
        }

        // Specific imageid
        elseif (Hobis_Api_Array_Package::populatedKey('imageId', $options)) {
            if ($collection->getId() === $options['imageId']) {
                $filteredPointers[] = $collection->getPointers();
            }
        }

        // Specific sizeCode
        elseif (Hobis_Api_Array_Package::populatedKey('sizeCode', $options)) {

            // Look for specific sizecode
            foreach ($collection->getPointers() as $pointer) {
                if ($pointer->getSizeCode() === $options['sizeCode']) {
                    $filteredPointers[] = $pointer;
                }
            }
        }

        // Default
        else {
            $filteredPointers[] = $collection->getPointers();
        }
    }

    return $filteredPointers;
}

/**
    * Method will resize image based on sizeCodes
    *
    * @param array $options
    * @return array
    */
public static function resize(array $options)
{
    //-----
    // Validate
    //-----
    if (!Hobis_Api_Array_Package::populatedKey('sourceFileUri', $options)) {
        throw new Hobis_Api_Exception(sprintf('Invalid $options[sourceFileUri] (%s)', serialize($options)));
    } elseif (!Hobis_Api_Array_Package::populatedKey('sizeCodes', $options)) {
        throw new Hobis_Api_Exception(sprintf('Invalid $options[sizeCodes] (%s)', serialize($options)));
    } 
    //-----

    // Localize
    $sourceFileUri  = $options['sourceFileUri'];
    $sizeCodes      = $options['sizeCodes'];

    // Init        
    $tmpDir     = Hobis_Api_Directory_Package::fromArray(array('tmp','resizedImages', md5(rand())));        
    $filename   = Hobis_Api_File_Package::getName($sourceFileUri);
    $extension  = Hobis_Api_File_Package::getExtension($sourceFileUri);
    $ogFileUri  = Hobis_Api_Directory_Package::fromArray(array($tmpDir, $filename . '_' . Hobis_Api_Image::SIZE_CODE_ORIGINAL . '.' . $extension));

    //-----
    // Handle original
    // No need to resize, but lets rename and move into tmpDir
    //-----
    $options = array(
        'destFileUri'      => $ogFileUri,
        'sourceFileUri'    => $sourceFileUri
    );

    // Copy from temp location to permanent store
    Hobis_Api_File_Package::copy($options);

    $resizedImageUris[Hobis_Api_Image::SIZE_CODE_ORIGINAL] = $ogFileUri;
    //-----        

    foreach ($sizeCodes as $sizeCode) {

        $thumb      = PhpThumbFactory::create($sourceFileUri);
        $resized    = false;

            switch ($sizeCode) {

                    case Hobis_Api_Image::SIZE_CODE_SMALL:

                                    $thumb->resize(Hobis_Api_Image::SMALL_WIDTH, Hobis_Api_Image::SMALL_HEIGHT);

                                    $tmpFile   = $filename . '_' . Hobis_Api_Image::SIZE_CODE_SMALL . '.' . $extension;
                                    $resized   = true;

                            break;

                    case Hobis_Api_Image::SIZE_CODE_MEDIUM:

                                    $thumb->resize(Hobis_Api_Image::MED_WIDTH, Hobis_Api_Image::MED_HEIGHT);

                                    $tmpFile   = $filename . '_' . Hobis_Api_Image::SIZE_CODE_MEDIUM . '.' . $extension;
                                    $resized   = true;

                            break;

                    case Hobis_Api_Image::SIZE_CODE_LARGE:

                                    $thumb->resize(Hobis_Api_Image::LARGE_WIDTH, Hobis_Api_Image::LARGE_HEIGHT);

                                    $tmpFile   = $filename . '_' . Hobis_Api_Image::SIZE_CODE_LARGE . '.' . $extension;
                                    $resized   = true;

                            break;
            }

            if (true === $resized) {

            $tmpFileUri = Hobis_Api_Directory_Package::fromArray(array($tmpDir, $tmpFile));

                    Hobis_Api_File_Package::touch(array('fileUri' => $tmpFileUri));

                            $thumb->save($tmpFileUri);                

                            $resizedImageUris[$sizeCode] = $tmpFileUri;
            }
    }

            return $resizedImageUris;
    }
}
<?php

class Hobis_Api_Needhay_Type_Image_Haystack extends Hobis_Api_Needhay_Haystack
{
    /**
     * This method will write asset contents to the haystack
     *  Image asset contents are comprised file uris
     * 
     * @throws Hobis_Api_Exception 
     */
    public function write()
    {
        // Validate
        if (!Hobis_Api_Array_Package::populated($this->getNeedle()->getNeedleCollections())) {
            throw new Hobis_Api_Exception(sprintf('Invalid $collections (%s)', serialize($this->getNeedle()->getNeedleCollections())));
        }
        
        $haystackFilePath = Hobis_Api_Needhay_Package::generatePath(
            array( 
                'store' => $this->getStore(),
                'type'  => Hobis_Api_Needhay::HAYSTACK
            )
        );        
        
        foreach ($this->getNeedle()->getNeedleCollections() as $needleCollection) {

            if ($needleCollection->getType() !== Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE) {
                continue;
            }

            foreach ($needleCollection->getPointerCollections() as $pointerCollection) {

                if ($pointerCollection->getMode() !== Hobis_Api_NeedHay_Pointer_Collection::MODE_ADD) {
                    continue;
                }
            
                foreach ($pointerCollection->getPointers() as $pointer) {
                
                    $haystackFileUri = Hobis_Api_Directory_Package::fromArray(array($haystackFilePath, $pointer->getAssetName()));
                
                    Hobis_Api_File_Package::copy(
                        array(
                            'sourceFileUri'     => $pointer->getAssetContent(),
                            'destFileUri'       => $haystackFileUri
                        )
                    );
                }
            }
        }           
    }
}

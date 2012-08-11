<?php

class Hobis_Api_Needhay_Type_Description_File_Xml_Reader
{
    /**
     * Wrapper method converting description xml doms to to a needle collection
     * 
     * @param object $dom
     * @return object
     */
    public function toCollection($dom)
    {
        $domCollections = $dom->getElementsByTagName(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION);
        
        $needleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $needleCollection->setType(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION);

        foreach ($domCollections as $domCollection) {

            $pointerCollection = new Hobis_Api_Needhay_Pointer_Collection();

            // Typecast as int b/c it came in as int, should go out as the same
            $collectionId   = (int) $domCollection->getElementsByTagName(Hobis_Api_Needhay_Store_Op_File_Xml::TAG_ID)->item(0)->nodeValue;
            
            $pointerCollection->setMode(Hobis_Api_Needhay_Pointer_Collection::MODE_READ);
            $pointerCollection->setId($collectionId);

            foreach ($domCollection->getElementsByTagName(Hobis_Api_Needhay_Store_Op_File_Xml::TAG_POINTER) as $domPointer) {
                
                $pointer = new Hobis_Api_Needhay_Type_Description_Pointer();
                
                $pointer->setAssetName($domPointer->getElementsByTagName(Hobis_Api_Needhay_Store_Op_File_Xml::TAG_ASSET_NAME)->item(0)->nodeValue);
                
                $pointerCollection->setPointer($pointer);
            }
            
            $needleCollection->setPointerCollection($pointerCollection);

            unset($pointers);
        }
        
        return $needleCollection;
    }
}
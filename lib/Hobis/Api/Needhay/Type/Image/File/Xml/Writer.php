<?php

class Hobis_Api_Needhay_Type_Image_File_Xml_Writer extends Hobis_Api_Needhay_Type_Image_File_Xml
{
    /**
     * Wrapper method for generating body of xml file which is image specific
     * 
     * @param object
     * @param object
     * @return object
     */
    public function generateBody(Hobis_Api_Xml_Writer $writer, Hobis_Api_NeedHay_Needle_Collection $needleCollection)
    {           
        foreach ($needleCollection->getPointerCollections() as $pointerCollection) {

            $writer->startElement(Hobis_Api_NeedHay_Needle_Collection::TYPE_IMAGE);
            
                $writer->writeElement(Hobis_Api_Needhay_Store_Op_File_Xml::TAG_ID, $pointerCollection->getId());
                
                $writer->startElement(Hobis_Api_Needhay_Store_Op_File_Xml::TAG_POINTERS);
            
            foreach ($pointerCollection->getPointers() as $pointer) {
                
                    $writer->startElement(Hobis_Api_Needhay_Store_Op_File_Xml::TAG_POINTER);
                        $writer->writeElement(parent::TAG_SIZE_CODE, $pointer->getSizeCode());
                        $writer->writeElement(Hobis_Api_Needhay_Store_Op_File_Xml::TAG_ASSET_NAME, $pointer->getAssetName());
                    $writer->endElement();
            }

                $writer->endElement();

            $writer->endElement();
        }

        return $writer;
    }
}

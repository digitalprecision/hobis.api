<?php

class Hobis_Api_Needhay_Store_Op_File_Xml_Reader extends Hobis_Api_Needhay_Store_Op_File_Xml
{
    /**
     * Wrapper method for reading contents of a needle file
     * 
     * @param string
     * @return string
     * @throws Exception 
     */
    public function read($needleFileUri)
    {
        $this->setNeedleFileUri($needleFileUri);        

        try {
            
            $reader = $this->getReader();

        } catch (Exception $e) { 

            switch ($e->getCode()) {
                
                case Hobis_Api_Exception::CODE_XML_READER_UNABLE_TO_OPEN:
                case Hobis_Api_Exception::CODE_XML_READER_UNABLE_TO_READ:

                    Hobis_Api_Log_Package::toErrorLog()->debug($e);

                    break;                
            }

            throw $e;
        }
        
        $needle = new Hobis_Api_Needhay_Needle();
        
        while ($reader->read()) {
            
            // If we come across an ad needle collection
            if ($reader->nodeIsElement(Hobis_Api_Needhay_Needle_Collection::TYPE_AD . 's')) {
                
                $dom        = $reader->expand();
                $adReader   = new Hobis_Api_Needhay_Type_Ad_File_Xml_Reader();
                
                $dom->preserveWhiteSpace = false;
                
                $needle->setNeedleCollection($adReader->toCollection($dom));
                
                continue;
            }
            
            // If we come across a description needle collection
            if ($reader->nodeIsElement(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION . 's')) {
                
                $dom                = $reader->expand();
                $descriptionReader  = new Hobis_Api_Needhay_Type_Description_File_Xml_Reader();
                
                $dom->preserveWhiteSpace = false;
                
                $needle->setNeedleCollection($descriptionReader->toCollection($dom));
                
                continue;
            }
            
            // If we come across an image needle collection
            if ($reader->nodeIsElement(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE . 's')) {
                
                $dom            = $reader->expand();
                $imageReader    = new Hobis_Api_Needhay_Type_Image_File_Xml_Reader();
                
                $dom->preserveWhiteSpace = false;
                
                $needle->setNeedleCollection($imageReader->toCollection($dom));
                
                continue;
            }
        }

        return $needle;
    }
}
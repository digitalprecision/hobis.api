<?php

class Hobis_Api_Needhay_Store_Op_File_Xml_Writer extends Hobis_Api_Needhay_Store_Op_File_Xml
{
    /**
     * Wrapper method for generating needle content
     *  This method will call various other methods to create the content
     *  necessary to write a complete needle file
     *
     * @param object
     * @param object
     * @return type
     */
    public function generateContent(Hobis_Api_Needhay_Needle $needle, Hobis_Api_Needhay_Store $store)
    {
        $writer = $this->generateHeader($store);

        foreach ($needle->getNeedleCollections() as $needleCollection) {

            switch ($needleCollection->getType()) {

                // Write an ad collection
                case Hobis_Api_Needhay_Needle_Collection::TYPE_AD:

                    $writer->startElement(Hobis_Api_Needhay_Needle_Collection::TYPE_AD . 's');

                    // Pass image specific content to image writer
                    $adWriter   = new Hobis_Api_Needhay_Type_Ad_File_Xml_Writer();
                    $writer     = $adWriter->generateBody($writer, $needleCollection);

                    $writer->endElement();

                    break;

                // Write a description collection
                case Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION:

                    $writer->startElement(Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION . 's');

                    // Pass description specific content to description writer
                    $descriptionWriter  = new Hobis_Api_Needhay_Type_Description_File_Xml_Writer();
                    $writer             = $descriptionWriter->generateBody($writer, $needleCollection);

                    $writer->endElement();

                    break;

                // Write an image collection
                case Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE:

                    $writer->startElement(Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE . 's');

                    // Pass image specific content to image writer
                    $imageWriter    = new Hobis_Api_Needhay_Type_Image_File_Xml_Writer();
                    $writer         = $imageWriter->generateBody($writer, $needleCollection);

                    $writer->endElement();

                    break;
            }
        }

        $writer = $this->generateFooter($writer);

        return $writer->flush();
    }

    /**
     * Wrapper method for generating header content of a needle file
     *
     * @param Hobis_Api_Needhay_Store $store
     * @return object
     */
    protected function generateHeader(Hobis_Api_Needhay_Store $store)
    {
        $writer = $this->getWriter();

        $writer->openMemory();

        $writer->setIndent(true);
        $writer->setIndentString(parent::TAB);

        $writer->startDocument(parent::VERSION, parent::ENCODING);

        $writer->startElement(parent::TAG_ROOT);

            // Writing needle location specific data so it will be self-healing
            //  in case a needle file gets lost, it will be able to generate
            //  it's own path
            $writer->writeElement(parent::TAG_CONTEXT, $store->getContext());
            $writer->writeElement(parent::TAG_OBJECT, $store->getObject());
            $writer->writeElement(parent::TAG_ID, $store->getId());

        return $writer;
    }

    /**
     * Wrapper method for generating footer content of a needle file
     *
     * @param object
     * @return object
     */
    protected function generateFooter(Hobis_Api_Xml_Writer $writer)
    {
        $writer->endElement();

        return $writer;
    }
}
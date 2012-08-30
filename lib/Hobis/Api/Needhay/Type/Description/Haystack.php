<?php

class Hobis_Api_Needhay_Type_Description_Haystack extends Hobis_Api_Needhay_Haystack
{
    /**
     * This method will write asset contents to the haystack
     *  Description asset contents are comprised of description objects
     *
     * @throws Hobis_Api_Exception
     */
    public function write()
    {
        // Validate
        if (!Hobis_Api_Array_Package::populated($this->getNeedle()->getNeedleCollections())) {
            throw new Hobis_Api_Exception(sprintf('Invalid $collections (%s)', serialize($this->getNeedle()->getNeedleCollections())));
        }

        $haystackPath = Hobis_Api_Needhay_Package::generatePath(
            array(
                'store' => $this->getStore(),
                'type'  => Hobis_Api_Needhay::HAYSTACK
            )
        );

        foreach ($this->getNeedle()->getNeedleCollections() as $needleCollection) {

            if ($needleCollection->getType() !== Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION) {
                continue;
            }

            foreach ($needleCollection->getPointerCollections() as $pointerCollection) {

                if ($pointerCollection->getMode() !== Hobis_Api_NeedHay_Pointer_Collection::MODE_ADD) {
                    continue;
                }

                foreach ($pointerCollection->getPointers() as $pointer) {

                    $assetFileUri = Hobis_Api_Directory_Package::fromArray(
                        array(
                            $haystackPath,
                            $pointer->getAssetName()
                        )
                    );

                    $assetContents[] = $pointer->getAssetContent();
                }
            }

            if (!isset($assetContents)) {
                throw new Hobis_Api_Exception(sprintf('Invalid $assetContents (%s)', serialize($assetContents)));
            }

            // Descriptions only have one file, so flatten the array
            $assetContents = $assetContents[0];

            // Prep array of scalars for write
            foreach ($assetContents as $id => $descriptionAttribute) {

                $description = new Hobis_Api_Needhay_Type_Description();

                $description->setId($id);
                $description->setText($descriptionAttribute[Hobis_Api_Needhay_Type_Description::TOKEN_TEXT]);
                $description->setTimestamp(time());

                if (Hobis_Api_Array_Package::populatedKey(Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_ID, $descriptionAttribute)) {
                    $description->setSourceId($descriptionAttribute[Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_ID]);
                }

                if (Hobis_Api_Array_Package::populatedKey(Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_URL, $descriptionAttribute)) {
                    $description->setSourceUrl($descriptionAttribute[Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_URL]);
                }

                $descriptions[] = $description->toArray();
            }

            $assetContents = json_encode($descriptions);

            Hobis_Api_File_Package::write(
                array(
                    'fileUri'   => $assetFileUri,
                    'content'   => $assetContents,
                    'mode'      => Hobis_Api_File::MODE_WRITE,
                    'dirPerms'  => Hobis_Api_Filesystem::PERMS_RWX__RWS__R_X
                )
            );
        }
    }

    /**
     * This method will read asset contents from haystack
     *  Asset file uri is passed in because it is generated via needle store
     *
     * @param string
     * @return array
     * @throws Hobis_Api_Exception
     */
    public function read($assetFileUri)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_String_Package::populated($assetFileUri)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $assetFileUri (%s)', $assetFileUri));
        } elseif (!Hobis_Api_File_Package::isFile($assetFileUri)) {
            throw new Hobis_Api_Exception(sprintf('$assetFileUri is not a valid file (%s)', $assetFileUri));
        }
        //-----

        return Hobis_Api_Needhay_Type_Description_Package::fromAnon(json_decode(file_get_contents($assetFileUri)));
    }
}
<?php

class Hobis_Api_Needhay_Type_Description_Package
{
    /**
     * Wrapper method for hydrating concrete objects from anon objects
     *
     * @param array
     * @return array
     */
    public static function fromAnon(array $anonObjects)
    {
        // Validate
        if (false === Hobis_Api_Array_Package::populated($anonObjects)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $anonObjects: %s', serialize($anonObjects)));
        }

        foreach ($anonObjects as $anonObject) {

            $description = new Hobis_Api_Needhay_Type_Description();

            $description->setId($anonObject->{Hobis_Api_Needhay_Type_Description::TOKEN_ID});
            $description->setText($anonObject->{Hobis_Api_Needhay_Type_Description::TOKEN_TEXT});
            $description->setSourceId($anonObject->{Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_ID});
            $description->setSourceUrl($anonObject->{Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_URL});
            $description->setTimestamp($anonObject->{Hobis_Api_Needhay_Type_Description::TOKEN_TIMESTAMP});

            $descriptions[] = $description;
        }

        return $descriptions;
    }
}
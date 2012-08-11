<?php

abstract class Hobis_Api_Needhay_Store_Adapter_File extends Hobis_Api_Needhay_Store_Adapter
{
    const EXTENSION_XML = 'xml';

    /**
     * Contiainer for needle file uri
     *
     * @var string
     */
    protected $needleFileUri;

    /**
     * Wrapper method for getting needle file extension
     *
     * @return string
     * @throws Hobis_Api_Exception
     */
    protected function getExtension()
    {
        switch (get_class($this)) {

            case 'Hobis_Api_Needhay_Store_Adapter_File_Xml':
                return self::EXTENSION_XML;
                break;

            default:
                throw new Hobis_Api_Exception(sprintf('Invalid Class (%s)', get_class($this)));
        }
    }

    /**
     * Wrapper method for determing needle file uri
     *
     * @return string
     */
    public function getNeedleFileUri()
    {
        if (!Hobis_Api_String_Package::populated($this->needleFileUri)) {
            $this->needleFileUri = Hobis_Api_Directory_Package::fromArray(
                array(
                    Hobis_Api_Needhay_Package::generatePath(
                        array(
                            'store' => $this->getStore(),
                            'type'  => Hobis_Api_Needhay::NEEDLE
                        )
                    ),
                    $this->getStore()->getId() . '.' . $this->getExtension()
               )
            );
        }

        return $this->needleFileUri;
    }

    /**
     * Wrapper method for deleting needle from filesystem
     * 
     * @param object
     */
    protected function deleteNeedle(Hobis_Api_Needhay_Needle $needle)
    {   
        $parentPath = Hobis_Api_Needhay_Package::generatePath(
            array(
                'depth' => Hobis_Api_Needhay::CONTEXT,
                'store' => $this->getStore(),
                'type'  => Hobis_Api_Needhay::NEEDLE
            )
        );
        
        Hobis_Api_File_Package::remove(
            array(
                'fileUri'   => $this->getNeedleFileUri(),
                'baseDir'   => $parentPath,
                'removeDir' => true
            )
        );
    }

    /**
     * Wrapper method for writing a needle file
     *
     * @param object
     */
    protected function writeNeedle(Hobis_Api_Needhay_Needle $needle)
    {
        Hobis_Api_File_Package::write(
            array(
                'fileUri'   => $this->getNeedleFileUri(),
                'content'   => $this->getWriter()->generateContent($needle, $this->getStore()),
                'mode'      => Hobis_Api_File::MODE_WRITE,
                'dirPerms'  => Hobis_Api_Filesystem::PERMS_RWX__RWS__R_X
            )
        );
    }

    /**
     * Wrapper method for reading a needle file
     *
     * @return object
     */
    protected function readNeedle()
    {
        return $this->getReader()->read($this->getNeedleFileUri());
    }
}
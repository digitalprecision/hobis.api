<?php

abstract class Hobis_Api_Needhay_Haystack
{
    const OP_CLEAN  = 'clean';
    const OP_WRITE  = 'write';
          
    /**
     * All child classes must know how to write their contents to haystack
     */
    abstract public function write();
    
    /**
     * Needle container
     * 
     * @var object
     */
    protected $needle;
    
    /**
     * Store container
     * 
     * @var object
     */
    protected $store;
    
    /**
     * Setter for needle
     * 
     * @param object
     */
    public function setNeedle(Hobis_Api_Needhay_Needle $needle)
    {
        $this->needle = $needle;
    }
    
    /**
     * Setter for store
     * 
     * @param object
     */
    public function setStore(Hobis_Api_Needhay_Store $store)
    {
        $this->store = $store;
    }
    
    /**
     * Getter for needle
     * 
     * @return object
     */
    public function getNeedle()
    {
        return $this->needle;
    }
    
    /**
     * Getter for store
     * 
     * @return object
     */
    public function getStore()
    {
        return $this->store;
    }
    
    /**
     * Wrapper method for removing files from haystack (filesystem)
     *
     * @throws Hobis_Api_Exception
     */
    public function clean()
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populated($this->getNeedle()->getNeedleCollections())) {
            throw new Hobis_Api_Exception(sprintf('Invalid $collections (%s)', serialize($this->getNeedle())));
        } elseif (!Hobis_Api_String_Package::populated($this->getStore()->getContext())) {
            throw new Hobis_Api_Exception(sprintf('Invalid $this->getStore()->getContext() (%s)', $this->getStore()->getContext()));
        }
        //-----        

        // Ensure we don't remove parent directories
        $parentPath = Hobis_Api_Needhay_Package::generatePath(
            array(
                'depth' => Hobis_Api_Needhay::CONTEXT, 
                'store' => $this->getStore(),
                'type'  => Hobis_Api_Needhay::HAYSTACK
            )
        );
        
        $haystackPath = Hobis_Api_Needhay_Package::generatePath(
            array( 
                'store' => $this->getStore(),
                'type'  => Hobis_Api_Needhay::HAYSTACK
            )
        );                                               

        foreach ($this->getNeedle()->getNeedleCollections() as $needleCollection) {

            foreach ($needleCollection->getPointerCollections() as $pointerCollection) {                

                foreach ($pointerCollection->getPointers() as $pointer) {                    

                    $haystackFileUri = Hobis_Api_Directory_Package::fromArray(
                        array(
                            $haystackPath,
                            $pointer->getAssetName()
                        )
                    );
    
                    try {
                        Hobis_Api_File_Package::remove(
                            array(
                                'fileUri'   => $haystackFileUri,
                                'baseDir'   => $parentPath,
                                'removeDir' => true
                            )
                        );
                    } catch (Exception $e) {
                        Hobis_Api_Log_Package::toErrorLog()->warn($e);
                        $e = null;
                    }
                }
            }
        }
    }
}
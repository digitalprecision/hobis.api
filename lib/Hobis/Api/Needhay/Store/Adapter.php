<?php

/**
 * Adapter represents the idea of flexibility in how needle data may be stored
 *  For example, needle data can be stored in xml format or, in future revisions
 *  in yaml format or even within a database
 */
abstract class Hobis_Api_NeedHay_Store_Adapter
{
    /**
     * Currently supported adapter formats
     */
    const TYPE_FILE_XML = 'xml';

    /**
     * Container for store object
     *  We need access to this so when writing data we can get access to
     *  pertinent storage data
     *
     * @var object
     */
    protected $store;

    /**
     * Force child classes to know how to delete a needle
     */
    abstract protected function deleteNeedle(Hobis_Api_Needhay_Needle $needle);

    /**
     * Force child classes to know how to read a needle
     */
    abstract protected function readNeedle();

    /**
     * Force child class to know how to write a needle
     */
    abstract protected function writeNeedle(Hobis_Api_Needhay_Needle $needle);

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
     * Factory method for creating reader objects based on child class
     *  instantiation
     *
     * @return object
     * @throws Hobis_Api_Exception
     */
    protected function getReader()
    {
        switch (get_class($this)) {

            case 'Hobis_Api_Needhay_Store_Adapter_File_Xml':
                return new Hobis_Api_Needhay_Store_Op_File_Xml_Reader();
                break;

            default:
                throw new Hobis_Api_Exception(sprintf('Unable to load reader, invalid child context (%s)', get_class($this)));
        }
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
     * Factory method for creating writer objects based on child class
     *  instantiation
     *
     * @return object
     * @throws Hobis_Api_Exception
     */
    protected function getWriter()
    {
        switch (get_class($this)) {

            case 'Hobis_Api_Needhay_Store_Adapter_File_Xml':
                return new Hobis_Api_Needhay_Store_Op_File_Xml_Writer();
                break;

            default:
                throw new Hobis_Api_Exception(sprintf('Unable to load writer, invalid child context (%s)', get_class($this)));
        }
    }

    /**
     * Wrapper method for deleting a needle
     * 
     * @param object
     */
    public function delete(Hobis_Api_Needhay_Needle $deleteNeedle)
    {
        $this->deleteNeedle($deleteNeedle);
    }

    /**
     * Wrapper method for reading needle data
     *
     * @return string
     */
    public function read()
    {
        return $this->readNeedle();
    }

    /**
     * Wrapper method for writing data to a needle
     *
     * @param object
     * @throws Exception
     * @throws Hobis_Api_Exception
     */
    public function write(Hobis_Api_Needhay_Needle $newNeedle)
    {
        // Init haystack needles
        //  These needle containers will house asset content which will be removed or written to haystack
        $removeNeedle   = new Hobis_Api_Needhay_Needle();
        $writeNeedle    = new Hobis_Api_Needhay_Needle();

        try {

            // See if there is an already existing needle file
            //  If it can't be opened or read, an exception will be thrown, and caught
            //  below, where a new needle will be created
            $oldNeedle = $this->read();

        } catch (Exception $e) {

            Hobis_Api_Log_Package::toErrorLog()->debug($e);

            // If code is anything other than open, throw it
            if ($e->getCode() !== Hobis_Api_Exception::CODE_XML_READER_UNABLE_TO_OPEN) {
                throw $e;
            }

            $e = null;

            $oldNeedle = new Hobis_Api_Needhay_Needle();
        }

        // Prep needle collection types
        $needleCollectionTypes = array_unique(array_merge(array_keys($oldNeedle->getNeedleCollections()), array_keys($newNeedle->getNeedleCollections())));

        // Merge collections
        //  The merging will result in two new collections, one for writing and one for removing
        foreach ($needleCollectionTypes as $needleCollectionType) {

            if (!in_array($needleCollectionType, Hobis_Api_Needhay_Package::$validNeedleCollectionTypes)) {
                    throw new Hobis_Api_Exception(sprintf('Invalid $needleCollectionType (%s)', $needleCollectionType));
            }

            $oldNeedleCollection = $oldNeedle->getNeedleCollection($needleCollectionType);
            $newNeedleCollection = $newNeedle->getNeedleCollection($needleCollectionType);

            // If old needle collection wasn't found create a footprint
            if (!($oldNeedleCollection instanceof Hobis_Api_Needhay_Needle_Collection)) {

                $oldNeedleCollection = new Hobis_Api_Needhay_Needle_Collection();

                $oldNeedleCollection->setType($needleCollectionType);
            }

            // If new needle collection wasn't found create a footprint
            if (!($newNeedleCollection instanceof Hobis_Api_Needhay_Needle_Collection)) {

                $newNeedleCollection = new Hobis_Api_Needhay_Needle_Collection();

                $newNeedleCollection->setType($needleCollectionType);
            }

            // Merge the pointer collections
            list($writeNeedleCollection, $removeNeedleCollection) = Hobis_Api_Needhay_Package::mergePointerCollections($oldNeedleCollection, $newNeedleCollection);

            // If $removeNeedleCollection data is available
            if (($removeNeedleCollection instanceof Hobis_Api_Needhay_Needle_Collection) &&
                (Hobis_Api_Array_Package::populated($removeNeedleCollection->getPointerCollections()))) {

                // Remove needle collection from needle file
                $removeNeedle->setNeedleCollection($removeNeedleCollection);
            }

            // If $writeNeedleCollection data is available
            if (($writeNeedleCollection instanceof Hobis_Api_Needhay_Needle_Collection) &&
                (Hobis_Api_Array_Package::populated($writeNeedleCollection->getPointerCollections()))) {

                // Add needle collection to needle file
                $writeNeedle->setNeedleCollection($writeNeedleCollection);
            }
        }

        // Write the needle file with write data
        $this->writeNeedle($writeNeedle);

        // Write haystack with haystack data
        $this->writeHaystack($writeNeedle, $removeNeedle);
    }

    /**
     * Wrapper method for writing haystack data
     *
     * @param object
     * @param object
     */
    protected function writeHaystack(Hobis_Api_Needhay_Needle $writeNeedle, Hobis_Api_Needhay_Needle $removeNeedle)
    {
        if (Hobis_Api_Array_Package::populated($removeNeedle->getNeedleCollections())) {
            Hobis_Api_Needhay_Haystack_Package::clean($removeNeedle, $this->getStore());
        }

        if (Hobis_Api_Array_Package::populated($writeNeedle->getNeedleCollections())) {
            Hobis_Api_Needhay_Haystack_Package::write($writeNeedle, $this->getStore());
        }
    }
}
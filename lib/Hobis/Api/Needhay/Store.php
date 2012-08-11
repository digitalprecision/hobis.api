<?php

/**
 * Store is used for locating a needle file
 */
class Hobis_Api_NeedHay_Store
{
    const ADAPTER_TYPE  = 'adapterType';
    const CONTEXT       = 'context';
    const ID            = 'id';
    const OBJECT        = 'object';
    /**
     * Needle store can access needles using various adapters (xml, yaml, mysql)
     *  This var dictates which adapter will be used when working with specified needle file
     *
     * @var string
     */
    protected $adapterType;

    /**
     * Context is used in determining where to store needles and related assets
     *  It is the top level non-flexible value
     *  This is comprable to an application (realestate) or a website (example.com)
     *  /flexible/path/to/context
     *
     * @var string
     */
    protected $context;

    /**
     * Id is used in determining where to store needles and related assets
     *  It is the last level non-flexible value
     *  This is comparable to a database primary key
     *  /flexible/path/to/context/object/id
     *
     * @var int
     */
    protected $id;

    /**
     * Object is used in determining where to store needles and related assets
     *  It is the mid level non-flexible value
     *  This is comprable to how database data might be related (i.e. A property for realestate app)
     *  /flexible/path/to/context/object
     *
     * @var string
     */
    protected $object;

    /**
     * Adapter factory
     *  This method will return an adapter based on preset adapterType
     *
     * @return object
     * @throws Hobis_Api_Exception
     */
    protected function getAdapter()
    {
        switch ($this->adapterType) {

            case Hobis_Api_Needhay_Store_Adapter::TYPE_FILE_XML:

                $adapter = new Hobis_Api_Needhay_Store_Adapter_File_Xml();

                break;

            default:
                throw new Hobis_Api_Exception(sprintf('Invalid $adapterType (%s)', $this->adapterType));
        }

        // Store information MUST be available, ensure adapter is store aware
        $adapter->setStore($this);

        return $adapter;
    }

    /**
     * Setter for adapter type
     *
     * @param string
     */
    public function setAdapterType($adapterType)
    {
        $this->adapterType = $adapterType;
    }

    /**
     * Setter for context
     *
     * @param string
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Setter for id
     *
     * @param int
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Setter for object
     *
     * @param string
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * Getter for adapter type
     *
     * @return string
     */
    public function getAdapterType()
    {
        return $this->adapterType;
    }

    /**
     * Getter for context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Getter for needle uri
     *
     * @return string
     */
    public function getNeedleUri()
    {
        return (is_callable(array($this->getAdapter(), 'getNeedleFileUri'))) ? $this->getAdapter()->getNeedleFileUri() : null;
    }

    /**
     * Getter for object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Wrapper method for deleting a needle file
     *
     * @param object
     */
    public function delete()
    {
        // Needle is adapater unaware
        //  It doesn't know how it came to be, it just is
        //  We need to delete at this level
        $needle = $this->read();

        // Need to remove all haystack assets first
        Hobis_Api_Needhay_Haystack_Package::clean($needle, $this);

        // Now we can delete the needle
        $this->getAdapter()->delete($needle);
    }

   /**
    * Wrapper method for reading a needle file
    *
    * @return object
    */
    public function read()
    {
        return $this->getAdapter()->read();
    }

    /**
     * Wrapper method for writing a needle file
     *
     * @param object
     */
    public function write(Hobis_Api_Needhay_Needle $needle)
    {
        return $this->getAdapter()->write($needle);
    }
}
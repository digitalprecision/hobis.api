<?php

class Hobis_Api_Needhay_Package
{
    /**
     * Container of valid dirs for validation
     *
     * @var array
     */
    public static $validDirs = array(
        Hobis_Api_Needhay::CONTEXT,
        Hobis_Api_Needhay::OBJECT
    );

    /**
     * Contianer of valid collection types for validation
     *
     * @var array
     */
    public static $validNeedleCollectionTypes = array(
        Hobis_Api_Needhay_Needle_Collection::TYPE_AD,
        Hobis_Api_Needhay_Needle_Collection::TYPE_DESCRIPTION,
        Hobis_Api_Needhay_Needle_Collection::TYPE_IMAGE
    );

    /**
     * Container of valid types for validation
     *
     * @var array
     */
    public static $validTypes = array(
        Hobis_Api_Needhay::HAYSTACK,
        Hobis_Api_Needhay::NEEDLE
    );

    /**
     * Singleton for flexible dir parts preset with default values
     *  These values represent the flexible portion of a fileuri which can be set to any path configuration
     *  Order of entry DOES matter
     *
     * @var array
     */
    protected static $flexibleDirs = array(
        'home',
        'db',
        'permfile',
        'app'
    );

    /**
     * Setter for allowing calling code to over-ride pre-defined flexible dirs
     *
     * @param array
     * @throws Hobis_Api_Exception
     */
    public static function setFlexibleDirs(array $dirs)
    {
        if (!Hobis_Api_Array_Package::populated($dirs)) {
            throw Hobis_Api_Exception(sprintf('Invalid $dirs (%s)', serialize($dirs)));
        }

        self::$flexibleDirs = $dirs;
    }

    /**
     * Wrapper method for removing the flexibleDirs section of an assetUri
     *
     * @param string
     * @return array
     */
    public static function getBasePathDirs($assetUri)
    {
        $assetUriDirs = Hobis_Api_Directory_Package::toArray($assetUri);

        foreach ($assetUriDirs as $key => $dir) {
            if (in_array($dir, self::$flexibleDirs)) {
                unset($assetUriDirs[$key]);
            }
        }

        return $assetUriDirs;
    }

    /**
     * Wrapper method for creating a webpath from an asseturi and prefixdirs
     *
     * @param string $assetUri
     * @param array $prefixDirs
     * @return string
     */
    public static function getWebPath($assetUri, array $prefixDirs)
    {
        return Hobis_Api_Directory_Package::fromArray(array_merge($prefixDirs, self::getBasePathDirs($assetUri)));
    }

    /**
     * Getter for flexible dirs as a path
     *
     * @return string
     */
    public static function getFlexiblePath()
    {
        return Hobis_Api_Directory_Package::fromArray(self::$flexibleDirs);
    }

    /**
     * Originally had this at needle file and haystack level, but didn't make sense
     *  to have the same logic in two diff placess o moved into single location
     *  This method will generate a haystack path for locating assets
     *
     * @param array
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function generatePath(array $options)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populated($options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options (%s)'), serialize($options));
        }

        elseif ((!Hobis_Api_Array_Package::populatedKey('store', $options)) ||
            (!($options['store'] instanceof Hobis_Api_Needhay_Store))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[store] (%s)', serialize($options)));
        }

        elseif (((!Hobis_Api_Array_Package::populatedKey('type', $options)) ||
            (!in_array($options['type'], Hobis_Api_Needhay_Package::$validTypes)))) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[type] (%s)', serialize($options)));
        }

        elseif (!Hobis_Api_String_Package::populated($options['store']->getContext())) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[store]->getContext() (%s)', serialize($options)));
        }

        elseif (!Hobis_Api_String_Package::populatedNumeric($options['store']->getId())) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[store]->getId() (%s)', serialize($options)));
        }

        elseif (!Hobis_Api_String_Package::populated($options['store']->getObject())) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[store]->getObject() (%s)', serialize($options)));
        }
        //-----

        //-----
        // Localize
        //-----

        // Depth determines how far into the uri we want to go, there were scenarios where
        //  we needed to delete assets from filesystem, but only up to a certain parent direct, depth helped
        //  us do that
        $depth = ((Hobis_Api_Array_Package::populatedKey('depth', $options)) && (in_array($options['depth'], self::$validDirs))) ? $options['depth'] : null;

        $store = $options['store'];

        // Type determines if we are looking for a needle (if adapter is set to file) or if we are looking for a haystack asset
        $type = ($options['type'] === Hobis_Api_NeedHay::HAYSTACK) ? Hobis_Api_NeedHay::HAYSTACK : Hobis_Api_NeedHay::NEEDLE;

        // DO NOT change any of the following values
        //  ORDER DOES MATTER from here on out
        $staticDirs = array(
            $type,
            $store->getContext()
        );

        $dirs = array_merge(self::$flexibleDirs, $staticDirs);

        if (Hobis_Api_Needhay::CONTEXT === $depth) {
            return Hobis_Api_Directory_Package::fromArray($dirs);
        }

        $dirs[] = $store->getObject();

        if (Hobis_Api_Needhay::OBJECT === $depth) {
            return Hobis_Api_Directory_Package::fromArray($dirs);
        }

        $dirs[] = Hobis_Api_Directory_Package::fromId($store->getId());

        return Hobis_Api_Directory_Package::fromArray($dirs);
    }

    /**
     * Wrapper method will merge pointer collections from a new needle collection
     *  with an old needle collection
     *  This method will determine which pointer collections are removed and which
     *  are added (based on mode action (_ADD or _REMOVE)
     *
     * @param object
     * @param object
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function mergePointerCollections(Hobis_Api_Needhay_Needle_Collection $oldNeedleCollection, Hobis_Api_Needhay_Needle_Collection $newNeedleCollection)
    {
        //-----
        // Validate
        //-----
        if ($oldNeedleCollection->getType() != $newNeedleCollection->getType()) {
            throw new Hobis_Api_Exception(sprintf('Invalid type (mismatch): old type (%s), new type (%s)', $oldNeedleCollection->getType(), $newNeedleCollection->getType()));
        } elseif (!in_array($oldNeedleCollection->getType(), self::$validNeedleCollectionTypes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid old type (%s)', $oldNeedleCollection->getType()));
        } elseif (!in_array($newNeedleCollection->getType(), self::$validNeedleCollectionTypes)) {
            throw new Hobis_Api_Exception(sprintf('Invalid new type (%s)', $newNeedleCollection->getType()));
        }
        //-----

        //-----
        // Init write and remove needle collections
        //-----

        // Write represents the data to be written
        $writeNeedleCollection = new Hobis_Api_Needhay_Needle_Collection();

        $writeNeedleCollection->setType($newNeedleCollection->getType());

        // Remove represents the data to be removed
        $removeNeedleCollection = Hobis_Api_Object_Package::cloneIt($writeNeedleCollection);
        //----

        //-----
        // Separate collections
        //-----

        // Init collection wrappers
        $newPointerCollections = array();
        $oldPointerCollections = array();

        foreach ($newNeedleCollection->getPointerCollections() as $pointerCollection) {
            $newPointerCollections[$pointerCollection->getId()] = $pointerCollection;
        }

        foreach ($oldNeedleCollection->getPointerCollections() as $pointerCollection) {
            $oldPointerCollections[$pointerCollection->getId()] = $pointerCollection;
        }
        //-----

        //-----
        // Shared Collection Handling
        //-----

        // Combine old and new based on pointer ids
        $sharedPointerCollectionIds = array_intersect_key($newPointerCollections, $oldPointerCollections);

        if (Hobis_Api_Array_Package::populated($sharedPointerCollectionIds)) {

            foreach ($sharedPointerCollectionIds as $sharedPointerCollectionId => $sharedPointerCollection) {

                // Localize
                $newPointerCollection = $newPointerCollections[$sharedPointerCollectionId];
                $oldPointerCollection = $oldPointerCollections[$sharedPointerCollectionId];

                // We only care about the new pointer collection mode
                switch ($newPointerCollection->getMode()) {

                    // Overwrite
                    case Hobis_Api_Needhay_Pointer_Collection::MODE_ADD:
                    case Hobis_Api_Needhay_Pointer_Collection::MODE_READ:

                        $writeNeedleCollection->setPointerCollection($newPointerCollection);

                        break;

                    // Remove
                    case Hobis_Api_Needhay_Pointer_Collection::MODE_REMOVE:

                        $removeNeedleCollection->setPointerCollection($oldPointerCollection);

                        break;
                }

                // Make sure footprint is removed from collection arrays
                unset($newPointerCollections[$sharedPointerCollectionId]);
                unset($oldPointerCollections[$sharedPointerCollectionId]);
            }
        }
        //-----

        //-----
        // New Collection Handling
        //-----
        if (Hobis_Api_Array_Package::populated($newPointerCollections)) {

            foreach ($newPointerCollections as $newPointerCollection) {

                switch ($newPointerCollection->getmode()) {

                    // Delete
                    case Hobis_Api_Needhay_Pointer_Collection::MODE_REMOVE:

                        continue;

                        break;

                    // Overwrite
                    case Hobis_Api_Needhay_Pointer_Collection::MODE_ADD:

                        $writeNeedleCollection->setPointerCollection($newPointerCollection);

                        break;
                }
            }
        }

        // Maintain existing
        if (Hobis_Api_Array_Package::populated($oldPointerCollections)) {
            foreach ($oldPointerCollections as $oldPointerCollection) {
                $writeNeedleCollection->setPointerCollection($oldPointerCollection);
            }
        }
        //-----

        return array($writeNeedleCollection, $removeNeedleCollection);
    }
}
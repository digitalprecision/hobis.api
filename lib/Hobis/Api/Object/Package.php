<?php

class Hobis_Api_Object_Package
{
    /**
     * Php's clone doesn't do deep clones by default
     *  By serializing an object it forces all sub-objects to be represented correctly
     *
     * @param object $object
     * @return object
     */
	public static function cloneIt($object)
	{
		return (!is_object($object)) ? null : unserialize(serialize($object));
	}
}
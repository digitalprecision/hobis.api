<?php

class Hobis_Api_Directory_Package
{
    /**
     * Wrapper method for changing mod on a directory
     * 
     * @param string
     * @param octal
     * @throws Hobis_Api_Exception
     */
    public static function chmod($dir, $perms)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($dir)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $dir: %s', $dir));
        }
        
        $status = chmod($dir, $perms);
        
        if (false === $status) {
            throw new Hobis_Api_Exception(sprintf('Unable to chmod file: %s | Attempted perms: %o', $dir, $perms));
        }
    }
    
    /**
     * This method will convert an array to a directory string
     *
     * @param array $array
     * @return string
     */
    public static function fromArray(array $array)
    {
        // Validate
        if (!Hobis_Api_Array_Package::populated($array)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $array (%s)', serialize($array)));
        }

        // Init
        $directory = null;

        foreach ($array as $element) {
            $directory .= (mb_substr($element, 0, 1) == DIRECTORY_SEPARATOR) ? $element : DIRECTORY_SEPARATOR . $element;
        }

        return $directory;
    }

    /**
     * Method will convert an id to a directory (split on every 2nd char)
     *
     * @param int $id
     * @return string
     */
    public static function fromId($id)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_String_Package::populatedNumeric($id)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $id (%s)', $id));
        } elseif ($id < 1) {
            throw new Hobis_Api_Exception(sprintf('$id (%s) must be positive', $id));
        }
        //-----

        $strLength = strlen($id);
        $strParts = array();

        for ($i = 0 ; $i < $strLength ; $i++) {

            if (($i % 2) === 0) {
                $strParts[] = substr($id, $i, 2);
            }
        }

        return self::fromArray($strParts);
    }

    /**
     * Convenience getter for returning actual timestamp using timestamp template
     *
     * @param string $unixTimestamp
     * @return string
     */
    public static function getFormattedTimestamp($unixTimestamp = null)
    {
        if (!Hobis_Api_String_Package::populated($unixTimestamp)) {
            $unixTimestamp = time();
        }

        return date(self::getTimestampTemplate(), $unixTimestamp);
    }

    /**
     * Convience getter for timestamp template
     *
     * @return string
     */
    public static function getTimestampTemplate()
    {
        $timestampTemplate = Hobis_Api_Directory_Package::fromArray(
            array(
                'Y',
                'm',
                'd',
                'h',
                'i',
                's'
            )
        );

        return $timestampTemplate;
    }

    /**
     * Convenience method for making a dir
     *
     * @param array $options
     */
    public static function make(array $options)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('dir', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $dir: %s', serialize($options)));
        } elseif (is_file($options['dir'])) {
            throw new Hobis_Api_Exception(sprintf('Cannot make dir because it already exists as a file: %s', $options['dir']));
        } elseif (is_dir($options['dir'])) {
            throw new Hobis_Api_Exception(sprintf('Cannot make dir because it already exists as a dir: %s', $options['dir']));
        }
        //-----

        // L & I
        $perms      = (Hobis_Api_Array_Package::populatedKey('perms', $options)) ? $options['perms'] : Hobis_Api_Filesystem::PERMS_RWX__RWX__R_X;
        $dir        = $options['dir'];
        $dirParts   = self::toArray($dir);
        
        // Need to determine which dir parts DNE
        while (!is_dir($dir)) {
            $nonExistentDirParts[]  = array_pop($dirParts);
            $dir                    = self::fromArray($dirParts);
        }
        
        // We popped off array elements so they are in reverse order, put em right
        $nonExistentDirParts = array_reverse($nonExistentDirParts);
        
        // Reset dirparts to new known existing dir
        $dirParts = self::toArray($dir);
        
        // Goal is to step through each subdir and create it separately
        //  This allows chmod to work correctly, if mkdir is called at once with full dir
        //  chmod will not affect higher level subdirs
        foreach ($nonExistentDirParts as $dirPart) {
            
            array_push($dirParts, $dirPart);
            
            $dir = self::fromArray($dirParts);
            
            if (!@mkdir($dir, $perms, true)) {
                throw new Hobis_Api_Exception(sprintf('Failed to create directory: %s.', $dir));
            }
            
            self::chmod($dir, $perms);
        }
    }

    /**
     * Convenience method for removing directories
     *  Basedir is passed in seperately so we don't have to substr anything
     *
     * @param string $baseDir
     * @param string $dirToRemove
     * @return bool
     * @throws Hobis_Api_Exception
     */
    public static function remove($baseDir, $dirToRemove)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_String_Package::populated($baseDir)) {
            throw new Hobis_Api_Exception('Invalid $baseDir');
        }

        elseif (!Hobis_Api_String_Package::populated($dirToRemove)) {
            throw new Hobis_Api_Exception('Invalid $dirToRemove');
        }

        // This scenario will happen, set code so calling code can skip it
        elseif ($baseDir === $dirToRemove) {

            $e = new Hobis_Api_Exception(sprintf('Cannot remove (%s)  because it is same as (%s)', $dirToRemove, $baseDir), Hobis_Api_Exception::CODE_DIR_BASE_EQUALS_REMOVE);

            throw $e;
        }
        //-----

        // Keep attempting to remove dir until we get to our base dir
        while ($baseDir !== $dirToRemove) {

            // Keeping quiet b/c dir may have other files in it
            $status = @rmdir($dirToRemove);

            // Our way out of recursion
            if (!$status) {

                Hobis_Api_Log_Package::toErrorLog()->info(sprintf('Unable to remove dir due to permissions issue: %s', $dirToRemove));

                break;
            }

            $dirToRemoveParts = self::toArray($dirToRemove);
            array_pop($dirToRemoveParts);
            $dirToRemove = self::fromArray($dirToRemoveParts);

            self::remove($baseDir, $dirToRemove);
        }
    }

    /**
     * Wrapper method for converting string to array based on dir separator
     *
     * @param string $string
     * @return array
     */
    public static function toArray($string)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($string)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $string (%s)', $string));
        }

        // Filtering so we remove any empty elements caused by preceding dir seps
        //  Calling array_values to reset the keys (made difference in unit tests)
        $array = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $string)));

        return $array;
    }
}
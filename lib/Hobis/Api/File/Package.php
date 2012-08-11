<?php

class Hobis_Api_File_Package
{
    public static $validWriteModes = array(
    	Hobis_Api_File::MODE_APPEND,
        Hobis_Api_File::MODE_CREATE,
    	Hobis_Api_File::MODE_WRITE
    );
    
    /**
     * Method for copying file from source to destination
     *
     * @param array $options
     */
    public static function copy(array $options)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('destFileUri', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[destFileUri] (%s)', serialize($options)));
        } elseif (!Hobis_Api_Array_Package::populatedKey('sourceFileUri', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $options[sourceFileUri] (%s)', serialize($options)));
        }
        //-----

        //-----
        // Localize
        //-----
        $destDirPerms    = (Hobis_Api_Array_Package::populatedKey('destDirPerms', $options)) ? $options['destDirPerms'] : Hobis_Api_Filesystem::PERMS_RWX__RWX__R_X;
        $destFilePerms   = (Hobis_Api_Array_Package::populatedKey('destFilePerms', $options)) ? $options['destFilePerms'] : Hobis_Api_Filesystem::PERMS_RW__RW__R;

        $destFileUri 	= $options['destFileUri'];
        $sourceFileUri  = $options['sourceFileUri'];
        //-----

        // Init
        $destFileInfo	= new SplFileInfo($destFileUri);
        $sourceFileInfo	= new SplFileInfo($sourceFileUri);

        // Make sure we have a source file
        if (!$sourceFileInfo->isFile()) {
            throw new Hobis_Api_Exception(sprintf('Source file "%s" does not exist.', $sourceFileUri));
        }

        if (!is_dir($destFileInfo->getPath())) {
            Hobis_Api_Directory_Package::make(
                array(
                    'dir'	=> $destFileInfo->getPath(),
                    'perms'	=> $destDirPerms
                )
            );
        }

        // copy the temp file to the destination file
        if (!@copy($sourceFileUri, $destFileUri)) {
            throw new Hobis_Api_Exception(sprintf('Unable to copy "%s" to "%s"', $sourceFileUri, $destFileUri));
        }

        // chmod our file
        if (!@chmod($destFileUri, $destFilePerms)) {
            throw new Hobis_Api_Exception(sprintf('Unable to chmod "%s" to "%s"', $destFileUri, $destFilePerms));
        }
    }

    /**
     * Will return filename of given fileUri
     *
     * @param string $fileuri
     * @return string
     */
    public static function getBaseName($fileUri)
    {
        // Validate
        if (!self::isFile($fileUri)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $fileUri (%s)', $fileUri));
        }

        return pathinfo($fileUri, PATHINFO_BASENAME);
    }

    /**
     * Will return extension of given file
     *
     * @param string $file
     * @return string
     */
    public static function getExtension($fileUri)
    {
    	// Validate
    	if (!self::isFile($fileUri)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $fileUri (%s)', $fileUri));
    	}

    	return pathinfo($fileUri, PATHINFO_EXTENSION);
    }

    /**
     * Will return filename of given fileUri
     *
     * @param string $fileuri
     * @return string
     */
    public static function getName($fileUri)
    {
        // Validate
        if (!self::isFile($fileUri)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $fileUri (%s)', $fileUri));
        }

        return pathinfo($fileUri, PATHINFO_FILENAME);
    }

    /**
     * Method for determining if string is file
     *  Wanted a way to standardize this type of checking so we could force usage of SplFileInfo
     *
     * @param string $file
     * @return bool
     * @throws Hobis_Api_Exception
     */
    public static function isFile($file)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($file)) {
            throw new Hobis_Api_Exception('Invalid $file');
        }

        $fileInfo = new SplFileInfo($file);

        return $fileInfo->isFile();
    }

    /**
     * Method for deleting file from filesystem
     *
     * @param array $options
     */
    public static function remove(array $options)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('fileUri', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid fileUri: (%s)', serialize($options)));
        }

        elseif ((Hobis_Api_Array_Package::populatedKey('removeDir', $options, true)) &&
                (!Hobis_Api_Array_Package::populatedKey('baseDir', $options))) {
            throw new Hobis_Api_Exception(sprintf('Invalid baseDir: (%s)', serialize($options)));
        }
        //-----

        // Localize
        $fileUri = $options['fileUri'];

        if (!self::isFile($fileUri)) {
            throw new Hobis_Api_Exception(sprintf('Invalid File, DNE: (%s)', $fileUri));
        }

        unlink($fileUri);

        if (Hobis_Api_Array_Package::populatedKey('removeDir', $options, true)) {

            $fileInfo = new SplFileInfo($fileUri);

            Hobis_Api_Directory_Package::remove($options['baseDir'], $fileInfo->getPath());
        }
    }

    /**
     * Touch file
     *  Updates mtime if exists, or will create if it does not
     *
     * @param array $options
     */
    public static function touch(array $options)
    {
        // Validate
        if (!Hobis_Api_Array_Package::populatedKey('fileUri', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid fileUri: (%s)', serialize($options)));
        }

        // Wanted to provide flexibility to allow calling code to pass in
        //  timestamp flags for touching a file, but if not passed in, set
        //  initial value so aTime and time can be the same
        $currentTimestamp = time();

        //-----
        // Localize
        //-----
        $aTimestamp = (Hobis_Api_Array_Package::populatedKey('aTimestamp', $options)) ? $options['aTimestamp'] : $currentTimestamp;
        $dirPerms   = (Hobis_Api_Array_Package::populatedKey('dirPerms', $options)) ? $options['dirPerms'] : Hobis_Api_Filesystem::PERMS_RWX__RWX__R_X;
        $fileUri    = $options['fileUri'];
        $filePerms  = (Hobis_Api_Array_Package::populatedKey('filePerms', $options)) ? $options['filePerms'] : Hobis_Api_Filesystem::PERMS_RW__RW__R;
        $timestamp  = (Hobis_Api_Array_Package::populatedKey('timestamp', $options)) ? $options['timestamp'] : $currentTimestamp;
        //-----

        $fileInfo = new SplFileInfo($fileUri);

        if ($fileInfo->isFile()) {
            throw new Hobis_Api_Exception(sprintf('File already exists: (%s)', $fileUri));
        }

        if (!is_dir($fileInfo->getPath())) {
            Hobis_Api_Directory_Package::make(
                array(
                    'dir'	=> $fileInfo->getPath(),
                    'perms'	=> $dirPerms
                )
            );
        }

        if (!@touch($fileUri, $timestamp, $aTimestamp)) {
            throw new Hobis_Api_Exception(sprintf('Unable to touch: (%s)', $fileUri));
        }

        elseif (!@chmod($fileUri, $filePerms)) {
            throw new Hobis_Api_Exception(sprintf('Unable to chmod: (%s) to (%s)', $fileUri, $filePerms));
        }

        elseif (!@self::isFile($fileUri)) {
            throw new Hobis_Api_Exception(sprintf('File DNE: (%s)', $fileUri));
        }
    }

    /**
     * Method for writing contents to a file
     *  This method will "touch" file if it does not exist
     *  $options used for touch can be passed through $options here
     *
     * @param array $options
     */
    public static function write($options)
    {
    	//-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('fileUri', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid fileUri: (%s)', serialize($options)));
        }

        elseif (!Hobis_Api_Array_Package::populatedKey('content', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid content: (%s)', serialize($options)));
        }
        
        elseif ((Hobis_Api_Array_Package::populatedKey('mode', $options)) &&
                (!in_array($options['mode'], Hobis_Api_File_Package::$validWriteModes))) {
            throw new Hobis_Api_Exception(sprintf('Invalid mode: (%s)', serialize($options)));
        }
        //-----

        // Localize
        $content    = $options['content'];
        $dirPerms   = (Hobis_Api_Array_Package::populatedKey('dirPerms', $options)) ? $options['dirPerms'] : Hobis_Api_Filesystem::PERMS_RWX__RWX__R_X;
        $fileUri    = $options['fileUri'];
        $filePerms  = (Hobis_Api_Array_Package::populatedKey('filePerms', $options)) ? $options['filePerms'] : Hobis_Api_Filesystem::PERMS_RW__RW__R;
        $mode       = (Hobis_Api_Array_Package::populatedKey('mode', $options)) ? $options['mode'] : Hobis_Api_File::MODE_WRITE;
        
        $fileInfo = new SplFileInfo($fileUri);
        
        if (!is_dir($fileInfo->getPath())) {
            Hobis_Api_Directory_Package::make(
                array(
                    'dir'	=> $fileInfo->getPath(),
                    'perms'	=> $dirPerms
                )
            );
        }
        
        $file = new SplFileObject($fileUri, $mode);

        if (!$file->flock(LOCK_EX | LOCK_NB)) {
            throw new Hobis_Api_Exception(sprintf('Unable to obtain lock on file: (%s)', $fileUri));
        } 

        elseif (!Hobis_Api_String_Package::populatedNumeric($file->fwrite($content))) {
            throw new Hobis_Api_Exception(sprintf('Unable to write content to file: (%s)... to (%s)', substr($content,0,25), $fileUri));
        }

        elseif (!$file->flock(LOCK_UN)) {
            throw new Hobis_Api_Exception(sprintf('Unable to remove lock on file: (%s)', $fileUri));
        }
        
        elseif (!@chmod($fileUri, $filePerms)) {
            throw new Hobis_Api_Exception(sprintf('Unable to chmod: (%s) to (%s)', $fileUri, $filePerms));
        }
    }
}
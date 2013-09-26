<?php

class Hobis_Api_Password_Package
{
    /**
     * Password hashing with PBKDF2.
     * Author: havoc AT defuse.ca
     * www: https://defuse.ca/php-pbkdf2.htm
     * 
     * Modified: Mike Purcell
     */
    
    /**
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
     * $algorithm - The hash algorithm to use. Recommended: SHA256
     * $password - The password.
     * $salt - A salt that is unique to the password.
     * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
     * $key_length - The length of the derived key in bytes.
     * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
     * Returns: A $key_length-byte key derived from the password and salt.
     *
     * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
     *
     * This implementation of PBKDF2 was originally created by https://defuse.ca
     * With improvements by http://www.variations-of-shadow.com
     */
    protected static function generateDerivedKey(array $modifiers)
    {
        //-----
        // Validate
        //-----
        if (false === Hobis_Api_Array_Package::populatedKey('algorithm', $modifiers)) {
            throw new Hobis_Api_Exception(sprintf('Invalid algorithm: %s', serialize($modifiers)));
        } elseif (false === Hobis_Api_Array_Package::populatedKey('iterationCount', $modifiers)) {
            throw new Hobis_Api_Exception(sprintf('Invalid iterationCount: %s', serialize($modifiers)));
        } elseif (false === Hobis_Api_Array_Package::populatedKey('derivedKeyLength', $modifiers)) {
            throw new Hobis_Api_Exception(sprintf('Invalid derivedKeyLength: %s', serialize($modifiers)));
        } elseif (false === Hobis_Api_Array_Package::populatedKey('password', $modifiers)) {
            throw new Hobis_Api_Exception(sprintf('Invalid password: %s', serialize($modifiers)));
        } elseif (false === Hobis_Api_Array_Package::populatedKey('salt', $modifiers)) {
            throw new Hobis_Api_Exception(sprintf('Invalid salt: %s', serialize($modifiers)));
        }
        //-----

        //Localize
        $algorithm          = strtolower($modifiers['algorithm']);
        $derivedKeyLength   = $modifiers['derivedKeyLength'];
        $iterationCount     = $modifiers['iterationCount'];
        $password           = $modifiers['password'];
        $rawOutput          = (true === Hobis_Api_Array_Package::populatedKey('rawOutput', $modifiers, true)) ? true : false;
        $salt               = $modifiers['salt'];
        
        if (false === in_array($algorithm, hash_algos(), true)) {
            throw new Hobis_Api_Exception(sprintf('Invalid algorithm: %s', $algorithm));
        } elseif ($iterationCount <= 0) {
            throw new Hobis_Api_Exception(sprintf('Invalid iterationCount: %d', $iterationCount));
        } elseif ($derivedKeyLength <= 0) {
            throw new Hobis_Api_Exception(sprintf('Invalid derivedKeyLength: %d', $derivedKeyLength));
        }

        $algorithmLength    = strlen(hash($algorithm, "", true));
        $output             = "";
        
        $blockCount = ceil($derivedKeyLength / $algorithmLength);
        
        for ($i = 1; $i <= $blockCount; $i++) {
            
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $iterationCount; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            
            $output .= $xorsum;
        }

        return (true === $rawOutput) ? substr($output, 0, $derivedKeyLength) : bin2hex(substr($output, 0, $derivedKeyLength));
    }
    
    /**
     * Wrapper method for generating a password hash based on pbkdf2 key derivation
     *  Result of this method is good for app consumption for storing password hashes in persistent stores
     *  for future validation
     * 
     * @param string
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function generateHash($password)
    {
        // Validate
        if (false === Hobis_Api_String_Package::populated($password)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $password: %s', $password));
        }

        $salt = base64_encode(mcrypt_create_iv(Hobis_Api_Password::LENGTH_BYTES_SALT, MCRYPT_DEV_URANDOM));

        $iterationCount = mt_rand(Hobis_Api_Password::ITERATION_RANGE_LOW, Hobis_Api_Password::ITERATION_RANGE_HIGH);

        $modifiers = array(
            'algorithm'         => Hobis_Api_Password::HASH_ALGORITHM,
            'iterationCount'    => $iterationCount,
            'derivedKeyLength'  => Hobis_Api_Password::LENGTH_BYTES_DERIVED_KEY,
            'password'          => $password,
            'rawOutput'         => true,
            'salt'              => $salt
        );

        $derivedKey = base64_encode(self::generateDerivedKey($modifiers));

        // format: algorithm:iterations:salt:hash
        return sprintf('%s:%d:%s:%s', Hobis_Api_Password::HASH_ALGORITHM, $iterationCount, $salt, $derivedKey);
    }
    
    /**
     * Wrapper method for comparing two strings in length-constant time
     *  Not sure what length-constant time refers too but looks linear
     * 
     * @param string
     * @param string
     * @return bool
     */
    protected static function slowComp($source, $target)
    {
        //-----
        // Validate
        //-----
        if (false === Hobis_Api_String_Package::populated($source)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $source: %s', $source));
        } elseif (false === Hobis_Api_String_Package::populated($target)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $target: %s', $target));
        }
        //-----
        
        $diff = strlen($source) ^ strlen($target);
        
        for($i = 0; $i < strlen($source) && $i < strlen($target); $i++) {
            $diff |= ord($source[$i]) ^ ord($target[$i]);
        }
        
        return $diff === 0; 
    }
    
    /**
     * Wrapper method for validating a password against a hash
     *  Useful for comparing user entered password against persistently stored password
     *  for authentication approval
     * 
     * @param string
     * @param string
     * @return bool
     * @throws Hobis_Api_Exception
     */
    public static function validate($password, $hash)
    {
        //-----
        // Validate
        //-----
        if (false === Hobis_Api_String_Package::populated($password)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $password: %s', $password));
        } elseif (false === Hobis_Api_String_Package::populated($hash)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $hash: %s', $hash));
        }
        //-----
        
        $hashParams = explode(":", $hash);
        
        if(count($hashParams) < Hobis_Api_Password::HASH_SECTION_COUNT) {
           throw new Hobis_Api_Exception(sprintf('Invalid $hashSections: %s', $hashParams));
        }
        
        $derivedKey = base64_decode($hashParams[Hobis_Api_Password::HASH_SECTION_INDEX_DERIVED_KEY]);
        
        $modifiers = array(
            'algorithm'         => $hashParams[Hobis_Api_Password::HASH_SECTION_INDEX_ALGORITHM],
            'iterationCount'    => (int)$hashParams[Hobis_Api_Password::HASH_SECTION_INDEX_ITERATION_COUNT],
            'derivedKeyLength'  => strlen($derivedKey),
            'password'          => $password,
            'rawOutput'         => true,
            'salt'              => $hashParams[Hobis_Api_Password::HASH_SECTION_INDEX_SALT]
        );
        
        return self::slowComp($derivedKey, self::generateDerivedKey($modifiers));
    }
}
<?php

class Hobis_Api_Seo_Package
{
    /**
     * Convenience method for converting search parameters to a string
     *  Arg string example: keyword1 AND keyword2 -"phrase 1" OR "phrase 2"
     *  Return string example: keyword1-and-keyword2-and-no-phrase~1-or-phrase~2
     *
     * @param string $searchString
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function toString($searchString)
    {
        if (!Hobis_Api_String_Package::populated($searchString)) {
            throw new Hobis_Api_Exception('Invalid $searchString');
        }

        // If something in the keywords has a quote it is a phrase
        //  Matching on "" or -""
        if (stripos($searchString, '"') !== false) {
			preg_match_all('/(-"|")(?:\\\\.|[^\\\\"])*"|\S+/', $searchString, $rawSearchStringParts);
        }

        // No quotes, just explode on spaces
        else {
			preg_match_all('/ (?:\\\\.|[^\\\\ ])* |\S+/', $searchString, $rawSearchStringParts);
		}

		// Flatten
		$searchStringParts = $rawSearchStringParts[0];

		// Filter out any empty elements
        $searchStringParts = array_filter($searchStringParts);

        // Set count
        $searchStringPartsCount  = count($searchStringParts);

        // Initialize
        $seoString      = null;
        $loopCount      = 0;

        // Mem-referenced b/c we are making changes to values
        foreach ($searchStringParts as &$searchStringPart) {

        	// Keep it clean
        	$searchStringPart = trim(strtolower($searchStringPart));

        	// If current keyword is a valid search operator, standardize it and add it to stack
        	if (in_array(strtolower($searchStringPart), Hobis_Api_Search_Package::$validSearchOperators)) {
        		$seoString .= strtolower($searchStringPart);
        	}

        	// Current keyword is not an operator, assume it's a keyword
        	else {

        		// Users can use the - to indicate that they don't want to include a keyword or phrase in their search
        		if (stripos($searchStringPart, Hobis_Api_Search::SEARCH_OPERATOR_NONE_CHAR) !== false) {

        		    // Remove the none char
        			$searchStringPart = str_replace(Hobis_Api_Search::SEARCH_OPERATOR_NONE_CHAR, '', $searchStringPart);

        			// We need to prefix search with AND operator so it makes more sense
        			//   vodka AND NO orange juice AND no cranberry juice
        			$seoString .= Hobis_Api_Search::SEARCH_OPERATOR_AND_TEXT . Hobis_Api_Search::DEFAULT_SEPARATOR;

        			// Add none text
        			$seoString .= Hobis_Api_Search::SEARCH_OPERATOR_NONE_TEXT . Hobis_Api_Search::DEFAULT_SEPARATOR;
        		}

        		// Only append the default search operator if this isn't the first keyword and the previous keyword
        		//	is not a search operator
        		elseif (($loopCount > 0) && (!in_array($searchStringParts[($loopCount - 1)], Hobis_Api_Search_Package::$validSearchOperators))) {
        			$seoString .= Hobis_Api_Search::DEFAULT_SEARCH_OPERATOR . Hobis_Api_Search::DEFAULT_SEPARATOR;
        		}

            	$seoString .= self::tokenize($searchStringPart);
        	}

            $seoString .= (++$loopCount != $searchStringPartsCount) ? Hobis_Api_Search::DEFAULT_SEPARATOR : null;
        }

        return $seoString;
    }

    /**
     * Override method for tokenizing value
     *
     * @param string $value
     * @return string
     */
    protected static function tokenize($value)
    {
    	$value = trim($value); // Exploding may cause space issues, get rid of spaces

    	// If a value was split on quotes we need to maintain that grouping
    	//  So if user submitted "orange juice" we maintain that pairing
    	//  Using ~ b/c it's unreserved and looks a lot like -
        $value = str_replace(' ', Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR,  $value);

        $tokenizedValue = Hobis_Api_String_Package::tokenize(
        	array(
        		'value' => $value,
        		'allowedChars' => array(
        			Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR
        		)
    		)
		);

		return $tokenizedValue;
    }

    /**
     * Convenience method for parsing an seo string for keywords
     *  This is useful when a user views a detail and we want to target company ads
     *  This assumes the string was built using self::toString()
     *
     * @param string
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function fromString($seoString)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($seoString)) {
            throw new Exception(sprintf('Invalid $seoString: %s', $seoString));
        }

        $dirtyKeywords = explode(Hobis_Api_Search::DEFAULT_SEPARATOR, $seoString);

        foreach ($dirtyKeywords as $keyword) {

            if ((Hobis_Api_Search::SEARCH_OPERATOR_AND_TEXT === $keyword) ||
                (Hobis_Api_Search::SEARCH_OPERATOR_OR_TEXT === $keyword)) {
                continue;
            }

            if (stripos($keyword, Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR) !== false) {
                $keyword = str_ireplace(Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR, ' ', $keyword);
            }

            $keywords[] = $keyword;
        }

        return (isset($keywords)) ? $keywords : array();
    }
}
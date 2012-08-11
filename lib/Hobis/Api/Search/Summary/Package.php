<?php

class Hobis_Api_Search_Summary_Package
{

    /**
     * Convience method
     *
     * @param unknown_type $seoString
     */
    public static function toString($seoString)
    {
        if (!Hobis_Api_String_Package::populated($seoString)) {
            throw new Hobis_Api_Exception('Invalid $seoString');
        }

        $seoParts = explode(Hobis_Api_Search::DEFAULT_SEPARATOR, $seoString);

        if (!Hobis_Api_Array_Package::populated($seoParts)) {
            return null;
        }

        // Filter out any empty elements
        $seoParts = array_filter($seoParts);

        // Initialize
        $summaryStringParts     = array();

        $seoConstants    = Hobis_Api_Search_Package::$validSearchOperators;
        $seoConstants[]  = Hobis_Api_Search::SEARCH_OPERATOR_NONE_TEXT;

        foreach ($seoParts as $seoPart) {

            // Keep it consistent
            $seoPart = strtolower($seoPart);

        	// If current element is a valid search operator, standardize it and add it to stack
        	if (in_array($seoPart, $seoConstants)) {
        		$summaryString = strtoupper($seoPart);
        	}

        	elseif (Hobis_Api_Search::SEARCH_OPERATOR_NONE_TEXT == $seoPart) {
	            $summaryString = ucwords(Hobis_Api_Search::SEARCH_OPERATOR_NONE_TEXT);
        	}

        	elseif (stripos($seoPart, Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR) !== false) {
        	    $seoPart = str_replace(Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR, ' ', $seoPart);
        	    $summaryString = '"' . $seoPart . '"';
        	}

        	else {
        	    $summaryString = $seoPart;
        	}

    	    $summaryStringParts[] = $summaryString;
        }

        $summaryString = implode(' ', $summaryStringParts);

        return $summaryString;
    }
}
<?php

class Hobis_Api_Search_Package
{
	/**
     * Defines the fields we want considered for highlighting
     *
     * @var array
     */
    public static $highlightFieldList = array(

    );

    /**
	 * Valid search operators
	 *
	 * @var array
	 */
	public static $validSearchOperators = array(
		'or',
		'and'
	);

    /**
     * Wrapper method for converting seo string back to search string
     *
     * @param string $seoString
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function toString($seoString)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_String_Package::populated($seoString)) {
            throw new Hobis_Api_Exception('Invalid $seoString');
        }
        //-----

        // Initialize
        $searchString   = null;
        $loopCounter    = 0;

        // Keywords should be in form of tokenized string
        $keywords         = explode(Hobis_Api_Search::DEFAULT_SEPARATOR, $seoString);
        $keywordsCount    = count($keywords);

        foreach ($keywords as &$keyword) {

            // If current keyword is same as none text, skip it
            if ($keyword == Hobis_Api_Search::SEARCH_OPERATOR_NONE_TEXT) {
                    $loopCounter++; // Keep it consistent
                    continue;
            }

            // Valid search operators are ok to pass through to search engine
            //	Oddly enough, they must be passed through in caps
            if (in_array($keyword, Hobis_Api_Search_Package::$validSearchOperators)) {
                    $keyword = strtoupper($keyword);
            }

            // If phrase seperator then we know it's a phrase, rebuild it as such
            elseif (mb_stripos($keyword, Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR) !== false) {
            $keyword = str_replace(Hobis_Api_Search::DEFAULT_PHRASE_SEPARATOR, ' ', $keyword);
            $keyword = '"' . $keyword . '"';
            }

            //	This is b/c the search engine understands '-keyword' and not 'no keyword'
            if (($loopCounter > 0) && ($keywords[($loopCounter - 1)] == Hobis_Api_Search::SEARCH_OPERATOR_NONE_TEXT)) {
                    $keyword = Hobis_Api_Search::SEARCH_OPERATOR_NONE_CHAR . $keyword;
            }

            $searchString .= $keyword;

            if (++$loopCounter != $keywordsCount) {
                $searchString .= ' ';
            }
        }

        return $searchString;
    }

	/**
	 * Wrapper method for getting count only from search engine
	 * 
	 * @param array $array
	 * @return array
	 * @throws Hobis_Api_Exception
	 */
	public static function getCountOnly(array $params)
	{
		//-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('siteId', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[siteId]');
        }

        elseif (!Hobis_Api_Array_Package::populatedKey('core', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[core]');
        }
		//-----
		
		$queryParameters = array(
            'q'		=> '*:*',
            'rows'	=> 0
        );
		
		// Do actual search
        $searchEngineInstance = Hobis_Api_Apache_Solr_Service_Package::getInstance(
            array(
                'core'      => $params['core'],
                'siteId'    => $params['siteId'],
                'context'   => Hobis_Api_Apache_Solr::CONTEXT_READONLY
            )
        );

        $searchEngineResponse = $searchEngineInstance->doSearch($queryParameters);

        // Add some debug componenents
        $response['debug']['querystring'] = $searchEngineResponse->getQuerystring();
        $response['debug']['requestType'] = $searchEngineResponse->getRequestType();

        if ($searchEngineResponse->getHttpStatus() != 200) {
            return $response;
        }

        // Writer Type is defaulted to phps (serialized), so unserialize
        //    And merge in with debug elements
        $rawResponse = unserialize($searchEngineResponse->getRawResponse());
		
		if ((Hobis_Api_Array_Package::populatedKey('response', $rawResponse)) &&
			(Hobis_Api_Array_Package::populatedKey('numFound', $rawResponse['response']))) {
			$numFound = $rawResponse['response']['numFound'];		
		} else {
			$numFound = -1;
		}
        
        $response['numFound'] = $numFound;

        return $response;
	}

    /**
     * Wrapper method for getting facets only from search engine
     *  Useful for browse searching
     * 
     * @param array $array
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function getFacetsOnly(array $params)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('siteId', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[siteId]');
        } elseif (!Hobis_Api_Array_Package::populatedKey('core', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[core]');
        } elseif (!Hobis_Api_Array_Package::populatedKey('facets', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[facets]');
        }
        //-----

        $queryParameters = array(
            'q'     => '*:*',
            'rows'  => 0
        );

        $queryParameters = array_merge($queryParameters, self::populateFacetParams($params['facets']));
        
        // Do actual search
        $searchEngineInstance = Hobis_Api_Apache_Solr_Service_Package::getInstance(
            array(
                'core'      => $params['core'],
                'siteId'    => $params['siteId'],
                'context'   => Hobis_Api_Apache_Solr::CONTEXT_READONLY
            )
        );

        $searchEngineResponse = $searchEngineInstance->doSearch($queryParameters);

        // Add some debug componenents
        $response['debug']['querystring'] = $searchEngineResponse->getQuerystring();
        $response['debug']['requestType'] = $searchEngineResponse->getRequestType();

        if ($searchEngineResponse->getHttpStatus() != 200) {
            return $response;
        }

        // Writer Type is defaulted to phps (serialized), so unserialize
        //    And merge in with debug elements
        $response = array_merge(unserialize($searchEngineResponse->getRawResponse()), $response);

        return $response;
    }

    /**
     * Wrapper method for returning search engine raw response based on $params
     *
     * @param array $searchParameters
     *
     * @return array
     * @throws Hobis_Api_Exception
     */
    public static function getRawResponse(array $params)
    {
        //-----
        // Validate
        //-----
        if (!Hobis_Api_Array_Package::populatedKey('siteId', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[siteId]');
        }

        elseif (!Hobis_Api_Array_Package::populatedKey('core', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[core]');
        }

        elseif (!Hobis_Api_Array_Package::populatedKey('keywords', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[keywords]');
        }

        elseif (!Hobis_Api_Array_Package::populatedKey('searchFields', $params)) {
            throw new Hobis_Api_Exception('Invalid $params[searchFields]');
        }

        elseif ((Hobis_Api_Array_Package::populatedKey('paginationContext', $params)) &&
                ($params['paginationContext'] == Hobis_Api_Search::PAGINATION_CONTEXT_DETAIL) &&
                (!Hobis_Api_Array_Package::populatedKey('paginationDirection', $params))) {
            throw new Hobis_Api_Exception('Invalid $params[paginationDirection]');
        }
        //-----

        //-----
        // Pagination
        //-----

        // Localize
        $currentPage        = (Hobis_Api_Array_Package::populatedKey('currentPage', $params)) ? $params['currentPage'] : Hobis_Api_Solr::DEFAULT_PAGE;
        $pageSize           = (Hobis_Api_Array_Package::populatedKey('pageSize', $params)) ? $params['pageSize'] : Hobis_Api_Solr::DEFAULT_ROWS;
        $paginationContext  = (Hobis_Api_Array_Package::populatedKey('paginationContext', $params)) ? $params['paginationContext'] : Hobis_Api_Search::PAGINATION_CONTEXT_RESULT;

        switch ($paginationContext) {

            case Hobis_Api_Search::PAGINATION_CONTEXT_DETAIL:

                $paginationDirection = $params['paginationDirection'];

                // Search engine context and set a wide range to avoid multiple queries
                $rows = $pageSize * 2;

                switch ($paginationDirection) {

                    case Hobis_Api_Search::PAGINATION_DIRECTION_PREVIOUS:

                        if ($currentPage == 1) {
                            $start = 0;
                        } else {
                            $start = (($currentPage - 1) * $pageSize) - $pageSize;
                        }

                        break;

                    case Hobis_Api_Search::PAGINATION_DIRECTION_NEXT:

                        $start = ($currentPage * $pageSize) - $pageSize;

                        break;

                    default:
                        throw new Hobis_Api_Exception('Invalid $paginationDirection');
                }

                break;

            case Hobis_Api_Search::PAGINATION_CONTEXT_RESULT;

                // Convert from app context to search engine context
                $start  = (($currentPage * $pageSize) - $pageSize);
                $rows   = $pageSize;

                break;

            default:
                throw new Hobis_Api_Exception('Invalid $paginationContext');
        }
        //-----

    	$queryParameters = array(
            'q'     => Hobis_Api_Search_Package::toString($params['keywords']),
            'fl'    => $params['searchFields'],
            'start' => ($start),
            'rows'  => ($rows)
        );

        //-----
        // Highlight
        //-----
        if (Hobis_Api_Array_Package::populatedKey('highlight', $params, true)) {
            $queryParameters['hl']				= 'true';
            $queryParameters['hl.fl']			= Hobis_Api_Search::HIGHLIGHT_FIELD;
            $queryParameters['hl.snippets']		= 25;
        }
        //-----

        //-----
        // Sortby
        //-----
        if (Hobis_Api_Array_Package::populatedKey('sortBy', $params)) {
            list ($sortField, $sortDirection) = explode(Hobis_Api_Search::SORT_BY_SEPARATOR, $params['sortBy']);
            $queryParameters['sort'] = $sortField . ' ' . $sortDirection;
        }
        //-----        

        //-----
        // Facets
        //-----

        // Set facet params
        $facetParameters = (Hobis_Api_Array_Package::populatedKey('facets', $params)) ? self::populateFacetParams($params['facets']) : array();

        // If there are any primary query modifiers, we need to add them to the primary query
        //  before we actually do a search
        if (Hobis_Api_Array_Package::populatedKey('pqm', $facetParameters)) {

            // Need to isolate the primary query so appending AND doesn't break
            $queryParameters['q'] = '(' . $queryParameters['q'] . ')';

            $queryParameters['q'] .= ' AND ' . implode(' AND ', $facetParameters['pqm']);

            // Keep it clean
            unset($facetParameters['pqm']);
        }
        //-----

        // Merge facet params into query params
        $queryParameters = array_merge($queryParameters, $facetParameters);

        // Do actual search
        $searchEngineInstance = Hobis_Api_Apache_Solr_Service_Package::getInstance(
            array(
                'core'      => $params['core'],
                'siteId'    => $params['siteId'],
                'context'   => Hobis_Api_Apache_Solr::CONTEXT_READONLY
            )
        );

        $searchEngineResponse = $searchEngineInstance->doSearch($queryParameters);

        // Add some debug componenents
        $rawResponse['debug']['querystring'] = $searchEngineResponse->getQuerystring();
        $rawResponse['debug']['requestType'] = $searchEngineResponse->getRequestType();

        if ($searchEngineResponse->getHttpStatus() != 200) {
            return $rawResponse;
        }

        // Writer Type is defaulted to phps (serialized), so unserialize
        //    And merge in with debug elements
        $rawResponse = array_merge(unserialize($searchEngineResponse->getRawResponse()), $rawResponse);

        return $rawResponse;
    }

    /**
     * Convenience method for populating facet params
     *
     * @param array $facetAttributes
     * @param array $selectedFacets
     * @return array
     */
    public static function populateFacetParams(array $facets)
    {
        if (!Hobis_Api_Array_Package::populated($facets)) {
            return array();
        }

        // Initialize
        $filterStrings              = array();
        $facetStrings               = array();
        $limitStrings               = array();
        $primaryQueryModifiers      = array();

        // Set some globals
        $facetParams['facet']           = 'true';
        $facetParams['facet.mincount']  = 1;

        foreach ($facets as $facetField => $facet) {

            // Set facet limit
            $facetLimit                = 'f.' . $facetField . '.facet.limit';
            $facetParams[$facetLimit]  = $facet['attributes']['limit'];

            // Set facet sort
            $facetSort                = 'f.' . $facetField . '.facet.sort';
            $facetParams[$facetSort]  = $facet['attributes']['sort'];

            // If we have selected facets we have to build strings to allow
            //  for tag and exclude
            if (Hobis_Api_Array_Package::populatedKey('selected', $facet)) {

                // Localize
                $fieldTag       = $facet['attributes']['tag'];
                $selectedFacets = $facet['selected'];

                // Facets are EXACT searches, so we need to wrap with quotes
                foreach ($selectedFacets as &$selectedFacet) {
                    $selectedFacet = '"' . $selectedFacet . '"';
                }

                //---
                // Filter Strings
                //---
                $filterString  = '{!tag=' . $fieldTag . '}' . $facetField . ':';
                $filterString .= implode(' AND ', $selectedFacets);

                $filterStrings[] = $filterString;
                //---

                //---
                // Append to original query strings
                //---
                $primaryQueryModifierString = $facetField . ':';
                $primaryQueryModifierString .= implode(' AND ', $selectedFacets);

                $primaryQueryModifiers[] = $primaryQueryModifierString;
                //---

                // Facet exclude Strings
                $facetStrings[] = '{!ex=' . $fieldTag . '}' . $facetField;
            }

            else {
                $facetStrings[] = $facetField;
            }
        }

        $facetParams['fq']          = $filterStrings;
        $facetParams['facet.field'] = $facetStrings;
        $facetParams['pqm']         = $primaryQueryModifiers;

        return $facetParams;
    }
}
<?php

class Hobis_Api_Search
{
	const DEFAULT_SEARCH_OPERATOR	= 'or';
	const DEFAULT_SEPARATOR			= '-';
	const DEFAULT_PHRASE_SEPARATOR	= '~';

	const SEARCH_OPERATOR_NONE_CHAR    = '-';
	const SEARCH_OPERATOR_NONE_TEXT    = 'no';

	const SEARCH_OPERATOR_OR_TEXT      = 'or';
	const SEARCH_OPERATOR_AND_TEXT     = 'and';

	const SORT_BY_SEPARATOR    = '_';

	// Shouldn't ever be more than one value
	const HIGHLIGHT_FIELD = 'SearchHighlight';

    const PAGINATION_CONTEXT_DETAIL = 'detail';
    const PAGINATION_CONTEXT_RESULT = 'result';

    const PAGINATION_DIRECTION_PREVIOUS = 'previous';
    const PAGINATION_DIRECTION_NEXT     = 'next';
}
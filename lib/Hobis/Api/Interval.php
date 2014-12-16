<?php

class Hobis_Api_Interval
{
	const DIRECTION_REVERSE	= 1;
	const DIRECTION_FORWARD = 2;
	
    const SECONDLY  = 1;
    const MINUTELY  = 2;
    const HOURLY    = 3;
    const DAILY     = 4;
    const WEEKLY    = 5;
    const MONTHLY   = 6;
    const YEARLY    = 7;
	
	// These are constructed in such a way that they can be passed straight into query constructs
	const DAY_1		= '1 DAY';
	const WEEK_1	= '1 WEEK';
}
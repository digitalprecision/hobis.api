<?php

class Hobis_Api_Time_Zone_Package
{
	/**
	 * Wrapper method for getting system timezone
	 * 
	 * @return string
	 */
	public static function getSystem()
	{
		return ini_get('date.timezone');
	}
	
	/**
	 * Wrapper method for converting a timestamp to timezone(s)
	 * 
	 * @param array
	 * 
	 * @return array
	 */
	public static function toZones($params)
	{
		//-----
		// Validate
		//-----
		
		// Trust me, you'll thank me later
		//	Not throwing exception, but will jam your logs until you fix :)
		if (Hobis_Api_Time_Zone::TOKEN_SHORT_UTC !== strtolower(self::getSystem())) {
			Hobis_Api_Log_Package::toErrorLog()->warn(sprintf('Warning, current system time is NOT UTC: %s', self::getSystem()));
		}
		//-----
		
		$timezones	= (true === Hobis_Api_Array_Package::populatedKey('timezones', $params)) ? $params['timezones'] : array(self::getSystem());
		$timestamp	= (true === Hobis_Api_Array_Package::populatedKey('timestamp', $params)) ? $params['timestamp'] : time();
		$format		= (true === Hobis_Api_Array_Package::populatedKey('format', $params)) ? $params['format'] : Hobis_Api_Time::FORMAT_DATE_TIME_ZONE_OFFSET_DEFAULT;
		
		foreach ($timezones as $timezone) {
			
			// May weaken this in the future to do string/int check to allow calls to pass either
			if (false === is_int($timestamp)) {
				
				Hobis_Api_Log_Package::toErrorLog(sprintf('Cannot convert tz for non-int timestamp: %s', serialize($timestamp)));
				
				$convertedTimezones[$timezone] = null;
				
				continue;
			}
			
			// So fukkn dumb, forcing constructor to take a string then having to set to a timestamp, should be other way around imo
			$dt = new DateTime('now', new DateTimeZone($timezone));
			
			$dt->setTimestamp($timestamp);
			
			$convertedTimezones[$timezone] = $dt->format($format);
		}

		return $convertedTimezones;
	}
}

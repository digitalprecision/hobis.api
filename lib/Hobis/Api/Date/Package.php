<?php

class Hobis_Api_Date_Package
{
    /**
     * Wrapper method for converting a string to timestamp
     * 
     * @param string
     * @param string
     * @return int
     * @throws Hobis_Api_Exception
     */
    public static function stringToTime($string, $timeZone = Hobis_Api_Time_Zone::TOKEN_SHORT_UTC)
    {   
        $date = new DateTime($string, new DateTimeZone($timeZone));
        
        return $date->getTimestamp();
    }
    
    /**
     * Wrapper method for converting diff between future date and past date into words
     *  Poached from cakephp api (and cleaned up) cake/TimeHelper.php
     *
     * @param object
     * @param object
     * @param array
     * @return string
     */
    public static function timeAgoInWords(Hobis_Api_I18N_DateTime $futureDateTime, Hobis_Api_I18N_DateTime $pastDateTime, array $options = array())
    {
        $backwards      = false;
        $format         = 'j/n/y';
        $relativeDate   = null;

        if (is_array($options)) {
            
            if (isset($options['format'])) {
                
                $format = $options['format'];
                
                unset($options['format']);
            }
        }
                
        $futureTime = $futureDateTime->getTimestamp();
        $pastTime   = $pastDateTime->getTimestamp();
        
        $diff = $futureTime - $pastTime;
                
        
        // If more than a week, then take into account the length of months
        if ($diff >= 604800) {
            
            $current    = array();
            $date       = array();
            $days       = 0;
            $hours      = 0;
            $minutes    = 0;
            $months     = 0;
            $seconds    = 0;
            $weeks      = 0;
            $years      = 0;

            list($future['H'], $future['i'], $future['s'], $future['d'], $future['m'], $future['Y']) = explode('/', date('H/i/s/d/m/Y', $futureTime));
            list($past['H'], $past['i'], $past['s'], $past['d'], $past['m'], $past['Y']) = explode('/', date('H/i/s/d/m/Y', $pastTime));

            if ($future['Y'] == $past['Y'] && $future['m'] == $past['m']) {
                
                $months = 0;
                $years  = 0;
                
            } else {
                
                if ($future['Y'] == $past['Y']) {
                    
                    $months = $future['m'] - $past['m'];
                    
                } else {
                    
                    $years = $future['Y'] - $past['Y'];
                    
                    $months = $future['m'] + ((12 * $years) - $past['m']);

                    if ($months >= 12) {
                        
                        $years = floor($months / 12);
                        $months = $months - ($years * 12);
                    }

                    if ($future['m'] < $past['m'] && $future['Y'] - $past['Y'] == 1) {
                        $years --;
                    }
                }
            }

            if ($future['d'] >= $past['d']) {
                
                $days = $future['d'] - $past['d'];
                
            } else {
                
                $daysInFutureMonth  = date('t', mktime(0, 0, 0, $future['m'] - 1, 1, $future['Y']));
                $daysInPastMonth    = date('t', $pastTime);

                if (!$backwards) {
                    
                    $days = ($daysInPastMonth - $past['d']) + $future['d'];
                    
                } else {
                    
                    $days = ($daysInFutureMonth - $past['d']) + $future['d'];
                }

                if ($future['m'] != $past['m']) {
                    $months --;
                }
            }

            if ($months == 0 && $years >= 1 && $diff < ($years * 31536000)) {
                
                $months = 11;
                $years --;
            }

            if ($months >= 12) {
                
                $months = $months - 12;
                $years  = $years + 1;
            }

            if ($days >= 7) {
                
                $weeks = floor($days / 7);
                
                $days = $days - ($weeks * 7);
            }
            
        } else {
            
            $days   = floor($diff / 86400);
            $years  = $months = $weeks = 0;

            $diff = $diff - ($days * 86400);
            
            $hours = floor($diff / 3600);
            
            $diff = $diff - ($hours * 3600);

            $minutes = floor($diff / 60);
            
            $diff = $diff - ($minutes * 60);
            
            $seconds = $diff;
        }
        
        $diff = $futureTime - $pastTime;

        // years and months and days
        if ($years > 0) {
            
            
            $relativeDate .= ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d year', '%d years', $years), $years);
            $relativeDate .= $months > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d month', '%d months', $months), $months) : '';
            $relativeDate .= $weeks > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d week', '%d weeks', $weeks), $weeks) : '';
            $relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d day', '%d days', $days), $days) : '';
        }
        
        // months, weeks and days
        elseif (abs($months) > 0) {

            $relativeDate .= ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d month', '%d months', $months), $months);
            $relativeDate .= $weeks > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d week', '%d weeks', $weeks), $weeks) : '';
            $relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d day', '%d days', $days), $days) : '';
        }
        
        // weeks and days
        elseif (abs($weeks) > 0) {

            $relativeDate .= ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d week', '%d weeks', $weeks), $weeks);
            $relativeDate .= $days > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d day', '%d days', $days), $days) : '';
        }
        
        // days and hours
        elseif (abs($days) > 0) {

            $relativeDate .= ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d day', '%d days', $days), $days);
            $relativeDate .= $hours > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d hour', '%d hours', $hours), $hours) : '';
        }
        
        // hours and minutes
        elseif (abs($hours) > 0) {

            $relativeDate .= ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d hour', '%d hours', $hours), $hours);
            $relativeDate .= $minutes > 0 ? ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d minute', '%d minutes', $minutes), $minutes) : '';
        }
        
        // minutes only
        elseif (abs($minutes) > 0) {

            $relativeDate .= ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d minute', '%d minutes', $minutes), $minutes);
        }
        
        // seconds only
        else {

            $relativeDate .= ($relativeDate ? ', ' : '') . sprintf(Hobis_Api_String_Package::toProperPlurality('%d second', '%d seconds', $seconds), $seconds);
        }
        
        return $relativeDate;
    }
}
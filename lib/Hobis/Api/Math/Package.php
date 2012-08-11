<?php

class Hobis_Api_Math_Package
{
	/**
	 * Converts number to a fraction
	 *
	 * @param mixed $number
	 * @return array
	 * @throws Hobis_Api_Exception
	 */
    public static function decimalToFractionAsArray($number)
    {
    	// Validate
        if (!Hobis_Api_String_Package::populatedNumeric($number)) {
    		throw new Hobis_Api_Exception(sprintf('Invalid $number:  (%s)', $number));
    	}

        // Localize
        $ogNumber   = (float) ($number);

        $wholeNumber    = floor($ogNumber);
        $decimal        = $ogNumber - $wholeNumber;

        if ($decimal == 0) {
            return array(
                'denom'         => 0,
                'numer'         => 0,
                'wholeNumber'   => $wholeNumber
            );
        }

        $quartersValue  = (float) ($decimal / Hobis_Api_Math::DECIMAL_QUARTERS);
        $numer          = null;
        $denom          = null;

        switch ((float) $quartersValue) {

            // 2/4, reduce it to 1/2
            case 2:
                $numer = 1;
                $denom = 2;
                break;

            // 1/4, 3/4
            case 1:
            case 3:
                $numer = $quartersValue;
                $denom = 4;
                break;
        }

        // 1/3, 2/3
        if ((!isset($numer)) ||
            (!isset($denom))) {
            $numer = ($decimal / Hobis_Api_Math::DECIMAL_THIRDS);
            $denom = 3;
        }

        $fractionParts = array(
            'denom'         => $denom,
            'numer'         => $numer,
            'wholeNumber'   => $wholeNumber
        );

        return $fractionParts;
    }

    /**
     * Wrapper method for converting a number to a fraction, and converting
     *  resulting array into a string
     *
     * @param array $options
     * @return string
     */
    public static function decimalToFractionAsString(array $options)
    {
        $decorate = (Hobis_Api_Array_Package::populatedKey('decorate', $options, true, true)) ? true : false;
        $number = (Hobis_Api_Array_Package::populatedKey('number', $options)) ? $options['number'] : null;

        $fractionParts = self::toFractionAsArray($number);

        $denom          = $fractionParts['denom'];
        $numer          = $fractionParts['numer'];
        $wholeNumber    = $fractionParts['wholeNumber'];

        if (true === $decorate) {
            $fraction = '<span class="numerator">' . $numer . '</span>&#8260;<span class="denominator">' . $denom . '</span>';
        } else {
            $fraction = $numer . '/' . $denom;
        }

        if ($wholeNumber >= 1) {
            $fraction = $wholeNumber . ' ' . $fraction;
        }

        return $fraction;
    }
}

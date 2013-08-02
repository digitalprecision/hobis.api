<?php

require_once 'Markdown.php';

use Michelf\Markdown;

class Hobis_Api_Markdown_Package
{
    /**
     * Wrapper method for marking up text which has been marked down
     *
     * @param string
     * @return string
     * @throws Hobis_Api_Exception
     */
    public static function markup($text)
    {
        // Validate
        if (!Hobis_Api_String_Package::populated($text)) {
            throw new Hobis_Api_Exception(sprintf('Invalid $text: (%s)', $text));
        }

	return Markdown::defaultTransform($text);
    }
}

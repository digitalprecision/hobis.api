<?php

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

        require_once 'markdown.php';

        return Markdown($text);
    }
}
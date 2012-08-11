<?php

class Hobis_Api_Xml_Reader extends XMLReader
{
    /**
     * Wrapper method for determining if node is an element
     *
     * @param string $tag - If present, will also check value
     * @return bool
     */
    public function nodeIsElement($tag = null)
    {
        if (is_null($tag)) {
            return (parent::ELEMENT === $this->nodeType);
        }

        return ((parent::ELEMENT === $this->nodeType) && ($tag === $this->localName)) ? true : false;
    }
}
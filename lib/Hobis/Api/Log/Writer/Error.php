<?php

class Hobis_Api_Log_Writer_Error extends Zend_Log_Writer_Abstract
{
    static public function factory($config)
    {
        return new self();
    }
    
    public function _write($event)
    {   
        error_log($this->_formatter->format($event));
    }
}
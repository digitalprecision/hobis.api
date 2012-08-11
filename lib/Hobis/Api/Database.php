<?php

/**
 * Wrapper class for Zend DB
 */
class Hobis_Api_Database extends Zend_Db
{
    const ADAPTER_PDO = 'Pdo_Mysql';

    const CONTEXT_READONLY      = 'ro';
    const CONTEXT_READWRITE     = 'rw';
}
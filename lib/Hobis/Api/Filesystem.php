<?php

class Hobis_Api_Filesystem
{
    const PERMS_R__R__R         = 0444;
    const PERMS_R_X__R_X__E     = 0550;
    const PERMS_RW__RW__R       = 0664;
    const PERMS_RWX__E__E       = 0700;
    const PERMS_RWX__RWX__R_X   = 0775;
    const PERMS_RWX__RWS__R_X   = 02775;
}
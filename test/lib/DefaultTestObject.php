<?php

class CoreTest_Lib_DefaultTestObject
{
    public function getId()
    {
        return 1;
    }

    public function __toString()
    {
        return get_class($this);
    }
}
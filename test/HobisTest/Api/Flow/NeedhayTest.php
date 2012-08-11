<?php

abstract class HobisTest_Api_Flow_NeedhayTest extends PHPUnit_Framework_TestCase
{
    protected $descriptions;
    protected $haystackPath;
    protected $id;
    protected $images;
    protected $needlePath;
    protected $object;
    protected $siteToken;

    protected $needle;
    protected $needleStore;

    //-----
    // Setup and teardown
    //-----
    protected function setup()
    {
        CoreLib_Api_Needhay_Package::setFlexibleDirs(
            array(
                'tmp',
                'unit_test',
                'needhay',
                md5(rand())
            )
        );

        $this->descriptions = array(
            array(
                CoreLib_Api_Needhay_Type_Description::TOKEN_TEXT        => 'Kahlúa is a *Mexican* coffee-flavored rum-based liqueur. It is dense and sweet, with the distinct taste of coffee, from which it is made. Kahlúa also contains sugar, corn syrup and vanilla bean.',
                CoreLib_Api_Needhay_Type_Description::TOKEN_SOURCE_ID   => 1,
                CoreLib_Api_Needhay_Type_Description::TOKEN_SOURCE_URL  => 'http://en.wikipedia.org/wiki/Kahlua',
                CoreLib_Api_Needhay_Type_Description::TOKEN_TIMESTAMP   => time()
            ),
            array(
                CoreLib_Api_Needhay_Type_Description::TOKEN_TEXT =>
                    'Drambuie is a sweet, golden colored 80-proof liqueur made from malt whisky, honey, herbs, and spices.

                    What if I have spaces N shit...

                    And more and

                    moar',
                CoreLib_Api_Needhay_Type_Description::TOKEN_SOURCE_ID   => 2,
                CoreLib_Api_Needhay_Type_Description::TOKEN_SOURCE_URL  => 'http://en.wikipedia.org/wiki/Drambuie',
                CoreLib_Api_Needhay_Type_Description::TOKEN_TIMESTAMP   => time()
            )
        );

        $this->id = 1;
        $this->object = 'object';
        $this->context = 'test';

        $this->needleStore = CoreLib_Api_Needhay_Store_Package::factory(
            array(
                'adapterType'   => CoreLib_Api_Needhay_Store_Adapter::TYPE_FILE_XML,
                'context'       => $this->context,
                'id'            => $this->id,
                'object'        => $this->object
            )
        );

        $this->needle = new CoreLib_Api_Needhay_Needle();

        $this->sourceImages = array(
            substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/TESTBED_1_acr_68.jpg',
            substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/TESTBED_2_acr_68.jpg'
        );

        $this->haystackPath = CoreLib_Api_Needhay_Package::generatePath(
            array(
                'store' => $this->needleStore,
                'type'  => CoreLib_Api_Needhay::HAYSTACK
            )
        );

        $this->needlePath = CoreLib_Api_Needhay_Package::generatePath(
            array(
                'store' => $this->needleStore,
                'type'  => CoreLib_Api_Needhay::NEEDLE
            )
        );
    }
    //-----
}
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
        Hobis_Api_Needhay_Package::setFlexibleDirs(
            array(
                'tmp',
                'unit_test',
                'needhay',
                md5(rand())
            )
        );

        $this->descriptions = array(
            array(
                Hobis_Api_Needhay_Type_Description::TOKEN_TEXT        => 'Kahlúa is a *Mexican* coffee-flavored rum-based liqueur. It is dense and sweet, with the distinct taste of coffee, from which it is made. Kahlúa also contains sugar, corn syrup and vanilla bean.',
                Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_ID   => 1,
                Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_URL  => 'http://en.wikipedia.org/wiki/Kahlua',
                Hobis_Api_Needhay_Type_Description::TOKEN_TIMESTAMP   => time()
            ),
            array(
                Hobis_Api_Needhay_Type_Description::TOKEN_TEXT =>
                    'Drambuie is a sweet, golden colored 80-proof liqueur made from malt whisky, honey, herbs, and spices.

                    What if I have spaces N shit...

                    And more and

                    moar',
                Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_ID   => 2,
                Hobis_Api_Needhay_Type_Description::TOKEN_SOURCE_URL  => 'http://en.wikipedia.org/wiki/Drambuie',
                Hobis_Api_Needhay_Type_Description::TOKEN_TIMESTAMP   => time()
            )
        );

        $this->id = 1;
        $this->object = 'object';
        $this->context = 'test';

        $this->needleStore = Hobis_Api_Needhay_Store_Package::factory(
            array(
                'adapterType'   => Hobis_Api_Needhay_Store_Adapter::TYPE_FILE_XML,
                'context'       => $this->context,
                'id'            => $this->id,
                'object'        => $this->object
            )
        );

        $this->needle = new Hobis_Api_Needhay_Needle();

        $this->sourceImages = array(
            substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/TESTBED_1_acr_68.jpg',
            substr(__FILE__, 0, strpos(__FILE__, '/Needhay')) . '/Needhay/_assets/TESTBED_2_acr_68.jpg'
        );

        $this->haystackPath = Hobis_Api_Needhay_Package::generatePath(
            array(
                'store' => $this->needleStore,
                'type'  => Hobis_Api_Needhay::HAYSTACK
            )
        );

        $this->needlePath = Hobis_Api_Needhay_Package::generatePath(
            array(
                'store' => $this->needleStore,
                'type'  => Hobis_Api_Needhay::NEEDLE
            )
        );
    }
    //-----
}
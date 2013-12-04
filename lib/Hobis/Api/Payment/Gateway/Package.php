<?php

class Hobis_Api_Payment_Gateway_Package
{
    /**
     * Container for prepared gateway objects
     *
     * @var array
     */
    protected static $gateways;
    
    /**
     * Factory method for returning payment gateway objects
     * 
     * @param array
     * @return object
     */
    public static function factory(array $options)
    {   
        //-----
        // Validate options
        if (false === Hobis_Api_Array_Package::populatedKey('mode', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid mode: %s', serialize($options)));
        } elseif (false === Hobis_Api_Array_Package::populatedKey('adapter', $options)) {
            throw new Hobis_Api_Exception(sprintf('Invalid adapter: %s', serialize($options)));
        }
        //-----
        
        // Localize
        $adapter    = $options['adapter'];
        $mode       = $options['mode'];
        
        // Construct container key
        $containerKey = md5(sprintf('%s_%s', $adapter, $mode));
        
        // Attempt to use singleton
        if (true === Hobis_Api_Array_Package::populatedKey($containerKey, self::$gateways)) {
            return self::$gateways[$containerKey];
        }
        
        $gateway    = new Hobis_Api_Payment_Gateway;
        $settings   = sfYaml::load(self::getConfig());        
        
        //-----
        // Validate config
        //-----
        if (false === Hobis_Api_Array_Package::populatedKey($mode, $settings)) {
            throw new Hobis_Api_Exception(sprintf('Invalid mode: %s', serialize($settings)));
        } elseif (false === Hobis_Api_Array_Package::populatedKey('apiKey', $settings[$mode])) {
            throw new Hobis_Api_Exception(sprintf('Invalid apiKey: %s', serialize($settings)));
        }
        //-----
    
        switch ($adapter) {
            
            case Hobis_Api_Payment_Gateway_Adapter::TYPE_PAYPAL:
                
                $gateway->setAdapter(new Hobis_Api_Payment_Gateway_Adapter_Paypal);
                
                break;
            
            default:
                throw new Hobis_Api_Exception(sprintf('Invalid adapter: %s', $adapter));
        }
        
        $gateway->getAdapter()->setMode($mode);
        $gateway->getAdapter()->setApiKey($settings[$mode]['apiKey']);
        
        self::$gateways[$containerKey] = $gateway;
        
        return $gateway;
    }
    
    /**
     * Wrapper method for getting config file
     *
     * @return string
     */
    protected static function getConfig()
    {
        return Hobis_Api_Directory_Package::fromArray(
            array(
                Hobis_Api_Environment_Package::getAppConfigPath(),
                'paymentGateway',
                'config.yml'
            )
        );
    }
}
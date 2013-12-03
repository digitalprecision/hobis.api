<?php

class Hobis_Api_Payment_Gateway_Package
{
    /**
     * Factory method for returning payment gateway objects
     * 
     * @param array
     * @return object
     */
    public static function factory(array $options)
    {
        // Static config for building purposes, will be moved to yaml config
        $config['dev']['apiKey'] = 'AbOtzhCLiF9be6fuPxWESk5jiFoMsI3t5AC5z9hfSFA6nUH7m8UutAOR01JE:EMRu2BB8PQacceugYjCFeNWxgVD3SJunCqaa0fOZ8qBc4-7DTvkyP3MlDdrX';
        
        $mode = $options['mode'];
        
        $gate = new Hobis_Api_Payment_Gateway;
    
        switch ($options['adapter']) {
            
            case Hobis_Api_Payment_Gateway_Adapter::TYPE_PAYPAL:
                
                $gate->setAdapter(new Hobis_Api_Payment_Gateway_Adapter_Paypal);
                
                break;
        }
        
        $gate->getAdapter()->setMode($mode);
        $gate->getAdapter()->setApiKey($config[$mode]['apiKey']);
        
        return $gate;
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
                'cache',
                'paymentGateway.yml'
            )
        );
    }
}
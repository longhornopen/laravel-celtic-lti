<?php

namespace LonghornOpen\LaravelCelticLTI\DataConnector;

class DataConnectorProviderFactory {
    public static function getDataConnectorProvider()
    {
        $dc_class = config('lti.data_connector_provider', LaravelDataConnectorProvider::class);
        return new $dc_class();
    }
}
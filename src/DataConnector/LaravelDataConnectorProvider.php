<?php

namespace LonghornOpen\LaravelCelticLTI\DataConnector;

use ceLTIc\LTI\DataConnector\DataConnector;
use Illuminate\Support\Facades\DB;
use LonghornOpen\LaravelCelticLTI\DataConnector\DataConnectorProvider;

class LaravelDataConnectorProvider implements DataConnectorProvider
{
    public function getDataConnector() {
        $connection_name = config('lti.connection_name', config('database.default'));
        return $this->getDataConnectorForConnection($connection_name);
    }

    public function getDataConnectorForConnection($connection_name)
    {
        $pdo = DB::connection()->getPdo();
        $dbTableNamePrefix = config('database.connections.' . $connection_name . '.prefix', '');
        return DataConnector::getDataConnector($pdo, $dbTableNamePrefix, 'pdo');
    }
}
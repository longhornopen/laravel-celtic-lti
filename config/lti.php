<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LTI 1.3 configuration
    |--------------------------------------------------------------------------
    |
    | LTI 1.3 tools will need, at minimum, RSA public and private keys.
    | These keys are multi-line files; if you're storing these in .env,
    | they can be quoted:
    |    LTI13_RSA_PRIVATE_KEY="-----BEGIN RSA PRIVATE KEY-----
    |    key contents here...
    |    -----END RSA PRIVATE KEY-----"
    |
    | * auto_register_deployment_id: Creating the Platform entry
    |   (using `php artisan lti:add_platform_1.3`) will require the
    |   deployment_id of at least one installed tool.  If set to true,
    |   future deployments of the tool on the same platform will have
    |   their deployment_ids automatically created as well.
    |
    | * required_scopes: The scope URLs of the LTI 1.3 services your app
    |   requires.
    |
    */

    'lti13' => [
        'signature_method' => env('LTI13_SIGNATURE_METHOD', 'RS256'),
        'key_id' => env('LTI13_KEY_ID', 'key-1'),
        'rsa_public_key' => env('LTI13_RSA_PUBLIC_KEY'),
        'rsa_private_key' => env('LTI13_RSA_PRIVATE_KEY'),
        'auto_register_deployment_id' => env('LTI13_AUTO_REGISTER_DEPLOYMENT_ID', false),
        'required_scopes' => [
            // sample scope URLs
            //"https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
            //"https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly",
            //"https://purl.imsglobal.org/spec/lti-ags/scope/score",
            //"https://purl.imsglobal.org/spec/lti-nrps/scope/contextmembership.readonly",
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Miscellaneous config
    |--------------------------------------------------------------------------
     */

    /*
        The name of the database connection (in config/databasee.php) which will be used
        as a data source.  By default, this is the same connection that's your 'default'
        in config/database.php.
     */

    // 'connection_name' => 'mysql'

    /*
        The Celtic LTI library requires a DataConnector class which provides access to the
        database.  We provide a default implementation of this class which uses the same database
        that you've configured for your Laravel app.  You can write your own custom class that
        implements the DataConnectorProvider interface and set it here.
    */

    // 'data_connector_provider' => LonghornOpen\LaravelCelticLTI\DataConnector\MyCustomDataConnectorProvider::class,

];
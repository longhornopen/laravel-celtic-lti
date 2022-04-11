<?php


namespace LonghornOpen\LaravelCelticLTI;


use ceLTIc\LTI;

class PlatformCreator
{

    /**
     * @param $consKey
     * @param LTI\DataConnector\DataConnector $dataConnector
     * @param $name
     * @param $secret
     */
    public static function createLTI1p2Platform(
        $consKey,
        LTI\DataConnector\DataConnector $dataConnector,
        $name,
        $secret
    ): void {
        $platform = LTI\Platform::fromConsumerKey($consKey, $dataConnector);
        if ($platform->created !== null) {
            throw new \RuntimeException("Platform with this key already exists. Refusing to overwrite.");
        }
        $platform->name = $name;
        $platform->secret = $secret;
        $platform->enabled = true;
        $platform->save();
    }

    public static function createLTI1p3Platform(
        LTI\DataConnector\DataConnector $dataConnector,
        $platform_id,
        $deployment_id,
        $client_id,
        $jku,
        $rsa_key,
        $signature_method,
        $authentication_url,
        $access_token_url,
        $authorization_server_id
    ): void {
        $platform = LTI\Platform::fromPlatformId($platform_id, $client_id, $deployment_id, $dataConnector);

        $platform->name = $platform_id;
        $platform->authorizationServerId = $authorization_server_id;
        $platform->jku = 'https://canvas.instructure.com/api/lti/security/jwks';
        $platform->authenticationUrl = 'https://canvas.instructure.com/api/lti/authorize_redirect';
        $platform->accessTokenUrl = 'https://canvas.instructure.com/login/oauth2/token';
        $platform->rsaKey = $rsa_key;
        $platform->signatureMethod = $signature_method;

        $platform->save();
        $platform->enabled = true;
    }

    public static function createLTI1p3PlatformCanvasCloud(
        LTI\DataConnector\DataConnector $dataConnector,
        $deployment_id,
        $client_id
    )
    {
        // FIXME: this as info help in Command
        /*

https://community.canvaslms.com/t5/Canvas-Question-Forum/LTI-1-3-Client-ID-and-deployment-id-to-differentiate-LMS-and/m-p/238813
// for client_id and deployment_id
https://canvas.instructure.com/doc/api/file.lti_dev_key_config.html

 * After you create and configure the Developer key for your app (see detailed instructions here) you can get both the client_id and the deployment_id.

The client_id is the developer key; it's a numeric value that looks something like

32450000000000542.

The deployment_id can be found in Admin > Settings | Apps | App Configurations. It is an alphanumeric string that might look something like 10832:7db538f4ca75c02373788b2ac73869cf572b0b68
 */

        $platform_id = 'https://canvas.instructure.com';
        $rsa_key = null;  // a public key is not required if a JKU is available
        $signature_method = 'RS256';

        $jku = 'https://canvas.instructure.com/api/lti/security/jwks';
        $authentication_url = 'https://canvas.instructure.com/api/lti/authorize_redirect';
        $access_token_url = 'https://canvas.instructure.com/login/oauth2/token';
        $authorization_server_id = null;  // defaults to the Access Token URL
        self::createLTI1p3Platform(
            $dataConnector,
            $platform_id,
            $deployment_id,
            $client_id,
            $jku,
            $rsa_key,
            $signature_method,
            $authentication_url,
            $access_token_url,
            $authorization_server_id
        );
    }
}
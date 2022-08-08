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
        $platform->jku = $jku;
        $platform->authenticationUrl = $authentication_url;
        $platform->accessTokenUrl = $access_token_url;
        $platform->rsaKey = $rsa_key;
        $platform->signatureMethod = $signature_method;

        $platform->enabled = true;
        $platform->save();
    }

    public static function createLTI1p3PlatformCanvasCloud(
        LTI\DataConnector\DataConnector $dataConnector,
        $deployment_id,
        $client_id
    )
    {
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

    public static function createLTI1p3PlatformMoodle(
        LTI\DataConnector\DataConnector $dataConnector,
        $deployment_id,
        $client_id,
        $platform_id
    )
    {
        $rsa_key = null;  // a public key is not required if a JKU is available
        $signature_method = 'RS256';

        $jku = $platform_id . "/mod/lti/certs.php";
        $authentication_url = $platform_id . "/mod/lti/auth.php";
        $access_token_url = $platform_id . "/mod/lti/token.php";

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
    public static function createLTI1p3PlatformSchoology(
        LTI\DataConnector\DataConnector $dataConnector,
        $deployment_id,
        $client_id
    )
    {
        $platform_id = 'https://schoology.schoology.com';
        $rsa_key = null;  // a public key is not required if a JKU is available
        $signature_method = 'RS256';

        $jku = 'https://lti-service.svc.schoology.com/lti-service/.well-known/jwks';
        $authentication_url = 'https://lti-service.svc.schoology.com/lti-service/authorize-redirect';
        $access_token_url = 'https://lti-service.svc.schoology.com/lti-service/access-token';
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
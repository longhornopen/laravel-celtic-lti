<?php

namespace LonghornOpen\LaravelCelticLTI;

use ceLTIc\LTI;
use ceLTIc\LTI\Context;
use ceLTIc\LTI\Jwt\Jwt;
use ceLTIc\LTI\Platform;
use ceLTIc\LTI\ResourceLink;
use ceLTIc\LTI\UserResult;
use Illuminate\Support\Facades\DB;

class LtiTool extends LTI\Tool
{
    protected $launchType = "";
    public const LAUNCH_TYPE_LAUNCH = 'launch';
    public const LAUNCH_TYPE_CONTENT_ITEM = 'content-item';

    protected static $singleton_tool = null;

    public static function getLtiTool() {
        if (self::$singleton_tool === null) {
            self::$singleton_tool = new LtiTool();
        }
        return self::$singleton_tool;
    }

    // Generally, you shouldn't construct this object yourself; use the
    // singleton provided by LtiTool::getLtiTool() instead.
    public function __construct($dataConnector = null)
    {
        if ($dataConnector === null) {
            $pdo = DB::connection()->getPdo();
            $dataConnector = LTI\DataConnector\DataConnector::getDataConnector($pdo, '', 'pdo');
        }
        parent::__construct($dataConnector);

        parent::$defaultTool = $this;
        $this->signatureMethod = config('lti.lti13.signature_method');
        $this->kid = config('lti.lti13.key_id');
        $this->rsaKey = config('lti.lti13.rsa_private_key');
        $this->requiredScopes = config('lti.lti13.required_scopes');

        if (config('lti.lti13.auto_register_deployment_id')) {
            $this->createDeploymentIdFromExistingPlatform();
        }
    }

    public function getDataConnector() : ceLTIc\LTI\DataConnector\DataConnector
    {
        return $this->dataConnector;
    }

    public function getUserResultById($id): UserResult
    {
        return UserResult::fromRecordId($id, $this->dataConnector);
    }

    public function getContextById($id): Context
    {
        return Context::fromRecordId($id, $this->dataConnector);
    }

    public function getResourceLinkById($id): ResourceLink
    {
        return ResourceLink::fromRecordId($id, $this->dataConnector);
    }

    public function getPlatformById($id): Platform
    {
        return Platform::fromRecordId($id, $this->dataConnector);
    }

    public function getLaunchType() : string
    {
        return $this->launchType;
    }

    protected function onLaunch() : void
    {
        $this->launchType = self::LAUNCH_TYPE_LAUNCH;
    }

    protected function onContentItem() : void
    {
        $this->launchType = self::LAUNCH_TYPE_CONTENT_ITEM;
    }

    /**
     * @throws LtiException
     */
    protected function onError() : void
    {
        throw new LtiException($this->reason);
    }

    /**
     * In the LTI 1.3 launch process, the LMS provides a `deployment_id`, specifying the
     * placement of a tool.  This deployment_id must be explicitly registered by hand as
     * part of the installation process.  The presence of a deployment_id is treated as
     * mandatory by the underlying CeLTIc library.  But for freely-available tools, the
     * deployment_id isn't really used - you don't really care to distinguish between
     * individual deployments of the tool (the way you would for billing purposes in
     * a commercial tool, say).
     *
     * This function allows tools to bypass CeLTIc's mandatory deployment_id checks, by
     * assuming that the tool has been successfully deployed once, and copying that
     * deployment with the new deployment_id provided in the request.  (Functionally, this
     * is likely the same thing you'd be asking your users to do by hand.)
     */
    public function createDeploymentIdFromExistingPlatform() : void
    {
        $messageParms = collect($this->getMessageParameters());
        $platform_id = $messageParms->get('platform_id'); //request('iss');
        $client_id = $messageParms->get('oauth_consumer_key'); //request('client_id');
        $deployment_id = $messageParms->get('deployment_id'); //request('lti_deployment_id');

        // if the JWT parms indicate a platform...
        if ($platform_id!==null && $client_id!==null && $deployment_id!==null) {
            $platform = DB::table('lti2_consumer')
                ->where('platform_id', $platform_id)
                ->where('client_id', $client_id)
                ->where('deployment_id', $deployment_id)
                ->get();
            // ... and it's not one we have yet
            if (count($platform) === 0) {
                $platform = DB::table('lti2_consumer')
                    ->where('platform_id', $platform_id)
                    ->where('client_id', $client_id)
                    ->get();
                // ... but which shares a platformID and clientID with an existing deployment
                if (count($platform) > 0) {
                    // ... clone that deployment, but with this deployment's deploymentID.
                    $new_platform = (array)$platform[0];
                    unset($new_platform['consumer_pk']);
                    $new_platform['deployment_id'] = $deployment_id;
                    DB::table('lti2_consumer')->insert($new_platform);
                    $this->platform = \ceLTIc\LTI\Platform::fromPlatformId($platform_id, $client_id, $deployment_id, $this->dataConnector);
                }
            }
        }
    }

    public function getJWKS()
    {
        $jwt = Jwt::getJwtClient();
        return $jwt::getJWKS($this->rsaKey, $this->signatureMethod, $this->kid);
    }
}

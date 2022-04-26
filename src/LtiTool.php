<?php

namespace LonghornOpen\LaravelCelticLTI;

use ceLTIc\LTI;
use Illuminate\Support\Facades\DB;

class LtiTool extends LTI\Tool
{
    protected $launchType = "";
    public const LAUNCH_TYPE_LAUNCH = 'launch';
    public const LAUNCH_TYPE_CONTENT_ITEM = 'content-item';

    public function __construct($dataConnector = null)
    {
        if ($dataConnector === null) {
            $pdo = DB::connection()->getPdo();
            $dataConnector = LTI\DataConnector\DataConnector::getDataConnector($pdo, '', 'pdo');
        }
        parent::__construct($dataConnector);
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
     * deployment with the new deployment_id provided in the request.  Functionally, this
     * is likely he same thing you'd be asking your users to do by hand.
     */
    public function createDeploymentIdFromExistingPlatform() : void
    {
        if (request('iss')!==null && request('client_id')!==null && request('lti_deployment_id')!==null) {
            $platform_id = request('iss');
            $client_id = request('client_id');
            $deployment_id = request('lti_deployment_id');
            $platform = DB::table('lti2_consumer')
                ->where('platform_id', $platform_id)
                ->where('client_id', $client_id)
                ->where('deployment_id', $deployment_id)
                ->get();
            if (count($platform) === 0) {
                $platform = DB::table('lti2_consumer')
                    ->where('platform_id', $platform_id)
                    ->where('client_id', $client_id)
                    ->get();
                if (count($platform) > 0) {
                    $new_platform = (array)$platform[0];
                    unset($new_platform['consumer_pk']);
                    $new_platform['deployment_id'] = $deployment_id;
                    DB::table('lti2_consumer')->insert($new_platform);
                }
            }
        }
    }
}

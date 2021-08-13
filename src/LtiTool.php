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
}

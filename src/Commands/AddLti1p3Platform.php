<?php

namespace LonghornOpen\LaravelCelticLTI\Commands;

use ceLTIc\LTI;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddLti1p3Platform extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lti:add_platform_1.3 {--lms_type=} {--lms_url=}';
    // FIXME use lms_url

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new LTI 1.3 platform';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        throw new \RuntimeException("LTI 1.3 not supported yet.");

        /*
        // FIXME move this to PlatformCreator
        $pdo = DB::connection()->getPdo();
        $dataConnector = LTI\DataConnector\DataConnector::getDataConnector($pdo);

        $platform_id = 'https://myschool.instructure.com';
        $client_id = 'test';
        $deployment_id = 'test';
        $platform = LTI\Platform::fromPlatformId($platform_id, $client_id, $deployment_id, $dataConnector);

        $platform->name = $platform_id;
        $platform->authorizationServerId = null;  // defaults to the Access Token URL
        if ($this->option('lms_type') === "canvas-cloud") {
            $platform->jku = 'https://canvas.instructure.com/api/lti/security/jwks';
            $platform->authenticationUrl = 'https://canvas.instructure.com/api/lti/authorize_redirect';
            $platform->accessTokenUrl = 'https://canvas.instructure.com/login/oauth2/token';
            $platform->rsaKey = null;  // a public key is not required if a JKU is available
            $platform->signatureMethod = 'RS256';
        } else {
            throw new \RuntimeException("Unknown lms type: " . $this->option('lms_type'));
        }

        $platform->save();
        $platform->enabled = true;

        return 0;
        */
    }
}

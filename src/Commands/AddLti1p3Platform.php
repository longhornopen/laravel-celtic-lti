<?php

namespace LonghornOpen\LaravelCelticLTI\Commands;

use ceLTIc\LTI;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use LonghornOpen\LaravelCelticLTI\PlatformCreator;

class AddLti1p3Platform extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lti:add_platform_1.3
                            {lms_type?}
                            {--client_id=}
                            {--deployment_id=}';

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
        if (!$this->argument('lms_type') || !$this->option('client_id') || !$this->option('deployment_id')) {
            $this->warn("Usage: php artisan lti:add_platform_1.3 {lms_type} --client_id=XXX --deployment_id=YYY");
            $this->warn("  lms_type can be one of:");
            $this->warn("    * 'canvas-cloud' - Cloud-hosted instances of Canvas LMS");
            $this->warn("    * 'moodle' - Moodle");
            $this->warn("    * 'schoology' - Schoology");
            $this->warn("    * 'blackboard-cloud' - Cloud-hosted instances of Blackboard");
            $this->warn("    * 'custom' - Any other LMS.");
            $this->warn('See https://github.com/longhornopen/laravel-celtic-lti/wiki/LTI-Key-Generation for the locations of the client and deployment IDs in your LMS.');
            return 0;
        }

        $pdo = DB::connection()->getPdo();
        $dataConnector = LTI\DataConnector\DataConnector::getDataConnector($pdo, '', 'pdo');
        $deployment_id = $this->option('deployment_id');
        $client_id = $this->option('client_id');

        if ($this->argument('lms_type') === 'canvas-cloud') {
            PlatformCreator::createLTI1p3PlatformCanvasCloud(
                $dataConnector,
                $deployment_id,
                $client_id);
            $this->info("Successfully created.");
            return 0;
        }

        if ($this->argument('lms_type') === 'moodle') {
            $this->info("You'll need the Platform ID of your Moodle instance.  It's probably the same as your Moodle instance's URL without a trailing slash.");
            $platform_id = $this->ask("What is your Moodle Platform ID?");
            PlatformCreator::createLTI1p3PlatformMoodle(
                $dataConnector,
                $deployment_id,
                $client_id,
                $platform_id
            );
            $this->info("Successfully created.");
            return 0;
        }

        if ($this->argument('lms_type') === 'schoology') {
            PlatformCreator::createLTI1p3PlatformSchoology(
                $dataConnector,
                $deployment_id,
                $client_id);
            $this->info("Successfully created.");
            return 0;
        }

        if ($this->argument('lms_type') === 'blackboard-cloud') {
            $application_id = $this->ask('What is the Application ID of this app in Blackboard?');
            PlatformCreator::createLTI1p3PlatformBlackboardCloud(
                $dataConnector,
                $deployment_id,
                $client_id,
                $application_id);
            $this->info("Successfully created.");
            return 0;
        }

        if ($this->argument('lms_type') === 'custom') {
            $this->info("In order to create a custom LTI 1.3 platform, you'll need to provide some info about your LMS.");
            $platform_id = $this->ask("What is your LMS's Platform ID?");
            $jku = $this->ask("What is your LMS's JSON Web Key Set URL (JKU)?");
            $rsa_key = null; // not needed if using JKU
            $signature_method = 'RS256'; // $this->anticipate("What signature method does your LMS use?  (Probably 'RS256')", ['RS256']);
            $authentication_url = $this->ask("What is your LMS's authentication URL?");
            $access_token_url = $this->ask("What is your LMS's access token URL?");
            $authorization_server_id = null; // defaults to the access token URL

            PlatformCreator::createLTI1p3Platform(
                $dataConnector,
                $platform_id,
                $deployment_id,
                $client_id,
                $jku,
                $rsa_key,
                $signature_method,
                $authentication_url,
                $access_token_url,
                $authorization_server_id);
            $this->info("Successfully created.");
            return 0;
        }

        $this->error("Unknown lms_type '".$this->argument('lms_type')."'");
        return 1;
    }
}

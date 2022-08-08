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
                            {--deployment_id=}
                            {--platform_id=}';

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
            if (!$this->option('platform_id')) {
                $this->error("Also provide a --platform_id=... option, giving the Platform ID of your Moodle instance.");
                $this->error("The Platform ID is probably the same as your Moodle instance's URL without a trailing slash.");
                return 1;
            }
            $platform_id = $this->option('platform_id');
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

        if ($this->argument('lms_type') === 'custom') {
            // FIXME hook up a bunch of command-line flags to the args in PlatformCreator::createLTI1p3Platform
            $this->error("Custom LTI 1.3 configurations not supported yet.  Use PlatformCreator directly for the moment.");
            $this->error("  (We're actively seeking contributions helping to support other LMSes natively.)");
            return 1;
        }

        $this->error("Unknown lms_type '".$this->argument('lms_type')."'");
        return 1;
    }
}

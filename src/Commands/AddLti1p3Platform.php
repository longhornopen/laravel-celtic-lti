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
                            {lms_type=custom : custom, canvas-cloud}
                            {--client_id=}
                            {--deployment_id=';

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
        $pdo = DB::connection()->getPdo();
        $dataConnector = LTI\DataConnector\DataConnector::getDataConnector($pdo, '', 'pdo');

        if ($this->argument('lms_type') === 'canvas-cloud') {
            PlatformCreator::createLTI1p3PlatformCanvasCloud(
                $dataConnector,
                $deployment_id,
                $client_id);
        } else if ($this->argument('lms_type') === 'custom') {
            throw new \RuntimeException("Custom LTI 1.3 configurations not supported yet.  Use PlatformCreator directly for the moment.");
        }
            throw new \RuntimeException("Unknown lms_type '".$this->argument('lms_type')."'");
        }
    }
}

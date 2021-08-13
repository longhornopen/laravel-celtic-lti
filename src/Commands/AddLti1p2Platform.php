<?php

namespace LonghornOpen\LaravelCelticLTI\Commands;

use ceLTIc\LTI;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use LonghornOpen\LaravelCelticLTI\PlatformCreator;

class AddLti1p2Platform extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lti:add_platform_1.2 {name} {key} {secret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new LTI 1.2 platform';

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

        $name = $this->argument('name');
        $consKey = $this->argument('key');
        $secret = $this->argument('secret');

        PlatformCreator::createLTI1p2Platform($consKey, $dataConnector, $name, $secret);

        $this->info("Successfully created LTI 1.2 integration as \'".$name."\'");

        return 0;
    }

}

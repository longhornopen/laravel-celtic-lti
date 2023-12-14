<?php

namespace LonghornOpen\LaravelCelticLTI\Commands;

use ceLTIc\LTI;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use LonghornOpen\LaravelCelticLTI\PlatformCreator;

class UpdateConsumerSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lti:update-consumer-setting {setting : the setting to update} {old-value : the old value of the setting} {new-value : the new value of the setting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a consumer setting for all consumers with a given value to a new value.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'setting' => 'Which LTI consumer setting should I update?',
            'old-value' => 'What value of that setting should I replace?',
            'new-value' => 'What value should I replace it with?',
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $total_processed = 0;
        DB::table('lti2_consumer')->orderBy('consumer_pk')->chunk(100, function ($consumers) use (&$total_processed) {
            foreach ($consumers as $consumer) {
                $settings = json_decode($consumer->settings, true);
                if (!array_key_exists($this->argument('setting'), $settings)) {
                    continue;
                }
                if ($settings[$this->argument('setting')] !== $this->argument('old-value')) {
                    continue;
                }
                $settings[$this->argument('setting')] = $this->argument('new-value');
                $consumer->settings = json_encode($settings);
                DB::update('UPDATE lti2_consumer SET settings=? WHERE consumer_pk=?', [$consumer->settings, $consumer->consumer_pk]);
                $total_processed++;
            }
        });

        $this->info("Successfully updated ".$total_processed." consumer settings.");
        return 0;
    }

}

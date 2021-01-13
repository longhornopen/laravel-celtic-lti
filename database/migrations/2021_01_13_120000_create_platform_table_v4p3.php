<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformTableV4P3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'lti2_tool',
            function (Blueprint $table) {
                $table->id('tool_pk');
                $table->string('name', 50);
                $table->string('consumer_key')->nullable();
                $table->string('secret', 1024)->nullable();
                $table->string('message_url')->nullable();
                $table->string('initiate_login_url')->nullable();
                $table->text('redirection_uris')->nullable();
                $table->text('public_key')->nullable();
                $table->string('lti_version', 10)->nullable();
                $table->string('signature_method', 15)->nullable();
                $table->text('settings')->nullable();
                $table->tinyInteger('enabled');
                $table->dateTime('enable_from')->nullable();
                $table->dateTime('enable_until')->nullable();
                $table->date('last_access')->nullable();
                $table->datetime('created');
                $table->datetime('updated');

                $table->unique(['initiate_login_url']);
            }
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lti2_tool');
    }
}
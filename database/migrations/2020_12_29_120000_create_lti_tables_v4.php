<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtiTablesV4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'lti2_consumer',
            function (Blueprint $table) {
                $table->id('consumer_pk');
                $table->string('name', 50);
                $table->string('consumer_key')->unique()->nullable();
                $table->string('secret', 1024)->nullable();
                $table->string('platform_id')->nullable();
                $table->string('client_id')->nullable();
                $table->string('deployment_id')->nullable();
                $table->text('public_key')->nullable();
                $table->string('lti_version', 10)->nullable();
                $table->string('signature_method', 15)->default('HMAC-SHA1');
                $table->string('consumer_name')->nullable();
                $table->string('consumer_version')->nullable();
                $table->string('consumer_guid', 1024)->nullable();
                $table->text('profile')->nullable();
                $table->text('tool_proxy')->nullable();
                $table->text('settings')->nullable();
                $table->tinyInteger('protected');
                $table->tinyInteger('enabled');
                $table->dateTime('enable_from')->nullable();
                $table->dateTime('enable_until')->nullable();
                $table->date('last_access')->nullable();
                $table->datetime('created');
                $table->datetime('updated');

                $table->unique(['platform_id', 'client_id', 'deployment_id']);
            }
        );

        Schema::create(
            'lti2_nonce',
            function (Blueprint $table) {
                $table->bigInteger('consumer_pk')->unsigned();
                $table->string('value', 50);
                $table->datetime('expires');

                $table->primary(['consumer_pk', 'value']);

                $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
            }
        );

        Schema::create(
            'lti2_access_token',
            function (Blueprint $table) {
                $table->bigInteger('consumer_pk')->unsigned();
                $table->text('scopes');
                $table->string('token', 2000);
                $table->datetime('expires');
                $table->datetime('created');
                $table->datetime('updated');

                $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
            }
        );

        Schema::create(
            'lti2_context',
            function (Blueprint $table) {
                $table->id('context_pk');
                $table->bigInteger('consumer_pk')->unsigned();
                $table->string('title')->nullable();
                $table->string('lti_context_id');
                $table->string('type', 50)->nullable();
                $table->text('settings')->nullable();
                $table->datetime('created');
                $table->datetime('updated');

                $table->index(['consumer_pk']);
                $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
            }
        );

        Schema::create(
            'lti2_resource_link',
            function (Blueprint $table) {
                $table->id('resource_link_pk');
                $table->bigInteger('context_pk')->unsigned()->nullable();
                $table->bigInteger('consumer_pk')->unsigned()->nullable();
                $table->string('title')->nullable();
                $table->string('lti_resource_link_id');
                $table->text('settings');
                $table->bigInteger('primary_resource_link_pk')->unsigned()->nullable();
                $table->tinyInteger('share_approved')->nullable();
                $table->datetime('created');
                $table->datetime('updated');

                $table->index(['consumer_pk']);
                $table->index(['context_pk']);

                $table->foreign('consumer_pk')->references('consumer_pk')->on('lti2_consumer');
                $table->foreign('context_pk')->references('context_pk')->on('lti2_context');
                $table->foreign('primary_resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
            }
        );

        Schema::create(
            'lti2_user_result',
            function (Blueprint $table) {
                $table->id('user_result_pk');
                $table->bigInteger('resource_link_pk')->unsigned();
                $table->string('lti_user_id');
                $table->string('lti_result_sourcedid', 1024);
                $table->datetime('created');
                $table->datetime('updated');

                $table->foreign('resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
                $table->index(['resource_link_pk']);
            }
        );

        Schema::create(
            'lti2_share_key',
            function (Blueprint $table) {
                $table->string('share_key_id', 32);
                $table->bigInteger('resource_link_pk')->unsigned();
                $table->tinyInteger('auto_approve');
                $table->datetime('expires');

                $table->primary('share_key_id');

                $table->foreign('resource_link_pk')->references('resource_link_pk')->on('lti2_resource_link');
                $table->index(['resource_link_pk']);
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
        Schema::dropIfExists('lti2_share_key');
        Schema::dropIfExists('lti2_user_result');
        Schema::dropIfExists('lti2_resource_link');
        Schema::dropIfExists('lti2_context');
        Schema::dropIfExists('lti2_access_token');
        Schema::dropIfExists('lti2_nonce');
        Schema::dropIfExists('lti2_consumer');
    }
}
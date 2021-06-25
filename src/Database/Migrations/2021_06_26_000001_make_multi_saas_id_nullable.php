<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeMultiSaasIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('pagebuilder.storage.database.prefix') . 'pages', function (Blueprint $table) {
            $table->integer('multi_saas_id')->unsigned()->nullable()->change();
        });
        Schema::table(config('pagebuilder.storage.database.prefix') . 'page_translations', function (Blueprint $table) {
            $table->integer('multi_saas_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('pagebuilder.storage.database.prefix') . 'pages', function (Blueprint $table) {
            $table->integer('multi_saas_id')->unsigned()->change();
        });

        Schema::table(config('pagebuilder.storage.database.prefix') . 'page_translations', function (Blueprint $table) {
            $table->integer('multi_saas_id')->unsigned()->change();
        });
    }
}

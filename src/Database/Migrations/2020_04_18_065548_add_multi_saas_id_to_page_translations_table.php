<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultiSaasIdToPageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('pagebuilder.storage.database.prefix') . 'page_translations', function (Blueprint $table) {
            $table->integer('multi_saas_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('pagebuilder.storage.database.prefix') . 'page_translations', function (Blueprint $table) {
            $table->dropColumn('multi_saas_id');
        });
    }
}

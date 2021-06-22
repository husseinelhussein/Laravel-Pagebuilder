<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultiSaasIdToPbUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('pagebuilder.storage.database.prefix') . 'uploads', function (Blueprint $table) {
            $table->addColumn('integer','multi_saas_id')->unsigned()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('pagebuilder.storage.database.prefix') . 'uploads', function (Blueprint $table) {
            $table->removeColumn('multi_saas_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaToPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('pagebuilder.storage.database.prefix') . 'pages', function (Blueprint $table) {
            $table->longText('meta')->nullable();
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
            $table->dropColumn('meta');
        });
    }
}

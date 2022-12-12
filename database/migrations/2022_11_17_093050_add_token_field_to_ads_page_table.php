<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenFieldToAdsPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads_page', function (Blueprint $table) {
            $table->string('token_name')->nullable()->after('external_page');
            $table->string('token_symbol')->nullable()->after('token_name');
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->longText('description')->change();
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads_page', function (Blueprint $table) {
            $table->dropColumn('token_name');
            $table->dropColumn('token_symbol');
        });
    }
}

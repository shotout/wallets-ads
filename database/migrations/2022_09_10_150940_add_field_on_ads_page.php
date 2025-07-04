<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldOnAdsPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads_page', function (Blueprint $table) {
            $table->string('telegram')->nullable()->after('facebook');
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
            $table->dropColumn(['telegram']); 
        });
    }
}

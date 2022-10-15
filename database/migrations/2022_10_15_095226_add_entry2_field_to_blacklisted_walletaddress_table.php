<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntry2FieldToBlacklistedWalletaddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blacklist_walletaddress', function (Blueprint $table) {
            $table->string('campaign_id')->nullable()->after('snooze_ads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blacklist_walletaddress', function (Blueprint $table) {
            $table->dropColumn('entry_id_subscribe');
            $table->dropColumn('campaign_id');
        });
    }
}

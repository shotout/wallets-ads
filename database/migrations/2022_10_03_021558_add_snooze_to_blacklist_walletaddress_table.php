<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSnoozeToBlacklistWalletaddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blacklist_walletaddress', function (Blueprint $table) {
            $table->tinyInteger('snooze_ads')->nullable()->after('walletaddress');
            $table->boolean('is_subscribe')->default(false)->after('walletaddress');
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
            $table->dropColumn('snooze_ads');
            $table->dropColumn('is_subscribe');
        });
    }
}

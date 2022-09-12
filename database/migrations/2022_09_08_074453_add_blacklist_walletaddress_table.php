<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlacklistWalletaddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'blacklist_walletaddress';
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('entry_id')->nullable();
            $table->string('walletaddress')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = 'blacklist_walletaddress';
        Schema::dropIfExists($table);
    }
}

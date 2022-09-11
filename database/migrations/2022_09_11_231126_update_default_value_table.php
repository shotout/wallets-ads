<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDefaultValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detailed_targeting', function (Blueprint $table) {
            $table->string('cryptocurrency_used')->default('0')->change();
            $table->string('account_age_year')->default('0')->change();
            $table->string('account_age_month')->default('0')->change();
            $table->string('account_age_day')->default('0')->change();
            $table->string('available_credit_wallet')->default('0')->change();
            $table->string('trading_volume')->default('0')->change();
            $table->string('amount_transaction')->default('0')->change();
            $table->string('amount_transaction_day')->default('0')->change();
            $table->string('nft_purchases')->default('0')->change();
            $table->string('airdrops_received')->default('0')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

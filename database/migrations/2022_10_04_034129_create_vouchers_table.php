<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->tinyInteger('type')->default(1);
            $table->string('value')->nullable();
            $table->string('min_budget')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->tinyInteger('status')->default(2);
            $table->timestamps();
        });

        Schema::create('user_voucher', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('voucher_id')->nullable();
            $table->integer('campaign_id')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        DB::table('vouchers')->insert([
            [
                "code" => "julian1234abcd",
                "value" => '50',
                "min_budget" => '1000',
                "created_at" => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers','user_voucher');
    }
}

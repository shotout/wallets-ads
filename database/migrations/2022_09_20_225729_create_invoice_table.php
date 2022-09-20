<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_id');
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('amount')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('payment_status')->default(0);
            $table->string('invoice_url')->nullable();
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
        Schema::dropIfExists('invoice');
    }
}

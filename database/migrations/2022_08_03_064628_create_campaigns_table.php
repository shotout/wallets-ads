<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();

            // ['ads_logo', 'ads_banner', 'ads_nft']
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();

            $table->string('name')->nullable();

            // type 1,2,3
            $table->tinyInteger('type')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->integer('count_click')->nullable();
            $table->integer('count_airdrop')->nullable();
            $table->integer('count_mint')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });

        Schema::create('audiences', function (Blueprint $table) {
            $table->id();
            $table->integer('campaign_id')->nullable();
            $table->integer('ads_id')->nullable();
            $table->integer('fe_id')->nullable();
            $table->integer('price')->nullable();
            $table->timestamps();
        });

        Schema::create('ads_page', function (Blueprint $table) {
            $table->id();
            $table->integer('campaign_id')->nullable();

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->string('website')->nullable();
            $table->string('discord')->nullable();
            $table->string('twitter')->nullable();
            $table->string('instagram')->nullable();
            $table->string('medium')->nullable();
            $table->string('facebook')->nullable();

            $table->string('external_page')->nullable();

            $table->timestamps();
        });

        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->integer('campaign_id')->nullable();

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });

        // optimized targeting -------------------
            Schema::create('optimized_targeting', function (Blueprint $table) {
                $table->id();
                $table->integer('audience_id')->nullable();

                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->float('price')->nullable();

                $table->timestamps();
            });
        // -----------------------------

        // balanced targeting -------------------
            Schema::create('balanced_targeting', function (Blueprint $table) {
                $table->id();
                $table->integer('audience_id')->nullable();

                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->float('price')->nullable();

                $table->integer('cryptocurrency_used')->nullable();

                $table->integer('account_age_year')->nullable();
                $table->integer('account_age_month')->nullable();
                $table->integer('account_age_day')->nullable();

                $table->integer('airdrops_received')->nullable();
                $table->integer('wallet_type')->nullable();
                $table->integer('location')->nullable();

                $table->timestamps();
            });

            // Schema::create('crypto_currencies', function (Blueprint $table) {
            //     $table->id();
            //     $table->string('name')->nullable();
            //     $table->timestamps();
            // });

            // Schema::create('airdrops_received', function (Blueprint $table) {
            //     $table->id();
            //     $table->string('name')->nullable();
            //     $table->timestamps();
            // });

            // Schema::create('wallet_type', function (Blueprint $table) {
            //     $table->id();
            //     $table->string('name')->nullable();
            //     $table->timestamps();
            // });

            // Schema::create('locations', function (Blueprint $table) {
            //     $table->id();
            //     $table->string('name')->nullable();
            //     $table->timestamps();
            // });
        // -------------------------------

        // detailed targeting -------------------
            Schema::create('detailed_targeting', function (Blueprint $table) {
                $table->id();
                $table->integer('audience_id')->nullable();

                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->float('price')->nullable();

                $table->integer('amount_transaction')->nullable();
                $table->integer('trading_volume')->nullable();
                $table->integer('available_credit_wallet')->nullable();
                $table->text('nft_purchases')->nullable();

                $table->timestamps();
            });
        // -----------------------------
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('audiences');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountFieldToAudienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audiences', function (Blueprint $table) {
            $table->integer('count_click')->default(null)->after('total_user');
            $table->integer('count_airdrop')->default(null)->after('count_click');
            $table->integer('count_mint')->default(null)->after('count_airdrop');
            $table->integer('count_view')->default(null)->after('count_mint');
            $table->integer('count_impression')->default(null)->after('count_view');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audiences', function (Blueprint $table) {
            $table->dropColumn('count_click');
            $table->dropColumn('count_airdrop');
            $table->dropColumn('count_mint');
            $table->dropColumn('count_view');
            $table->dropColumn('count_impression');
        });
    }
}

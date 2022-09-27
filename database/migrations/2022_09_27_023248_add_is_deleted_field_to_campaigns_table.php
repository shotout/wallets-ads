<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDeletedFieldToCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(false)->after('status');
        });

        Schema::table('audiences', function (Blueprint $table) {
            $table->string('entry_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('audiences', function (Blueprint $table) {
            $table->dropColumn('entry_id');
        });
    }
}

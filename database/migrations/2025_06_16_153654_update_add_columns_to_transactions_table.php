<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('order_id')->nullable()->change();
            $table->integer('user_id')->nullable()->change();
            $table->integer('amount')->nullable()->change();
			$table->string('guest_uid')->after('user_id')->nullable()->comment('generate uid for guests');
			$table->text('raw_payload')->after('amount')->nullable()->comment('for debugging');
			$table->integer('status_code')->after('amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('order_id')->change();
            $table->integer('user_id')->change();
            $table->integer('amount')->change();
			$table->dropColumn('guest_uid');
			$table->dropColumn('raw_payload');
			$table->dropColumn('status_code');
        });
    }
};

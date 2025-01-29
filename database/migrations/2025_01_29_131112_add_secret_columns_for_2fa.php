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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_secret_2fa_key')->nullable()->after('password'); // Add the 2FA secret key
            $table->boolean('enabled_2fa')->default(false)->after('google_secret_2fa_key'); // Add the enabled 2FA flag
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_secret_2fa_key'); // Remove the 2FA secret key
            $table->dropColumn('enabled_2fa'); // Remove the enabled 2FA flag
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use jdavidbakr\MailTracker\MailTracker;

class AddFirstOpenAndFirstClickColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection((new SentEmail())->getConnectionName())->table(config('mail-tracker.table-name', 'sent_emails'), function (Blueprint $table) {
            $table->datetime('clicked_at')->nullable()->after('meta');
            $table->datetime('opened_at')->nullable()->after('meta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection((new SentEmail())->getConnectionName())->table(config('mail-tracker.table-name', 'sent_emails'), function (Blueprint $table) {
            $table->dropColumn('opened_at');
            $table->dropColumn('clicked_at');
        });
    }
}

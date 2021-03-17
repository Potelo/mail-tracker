<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use jdavidbakr\MailTracker\Model\SentEmail;
use Illuminate\Database\Migrations\Migration;

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

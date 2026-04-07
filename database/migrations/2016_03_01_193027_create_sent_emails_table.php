<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use jdavidbakr\MailTracker\MailTracker;

class CreateSentEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(MailTracker::sentEmailModel()->getConnectionName())->create(config('mail-tracker.table-name', 'sent_emails'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('hash', 32)->unique();
            $table->text('headers')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('subject')->nullable();
            $table->integer('opens')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('user_id')->index()->nullable();
            $table->string('mailable')->nullable();
            $table->string('message_id')->nullable();
            $table->text('meta')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'mailable']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(MailTracker::sentEmailModel()->getConnectionName())->drop(config('mail-tracker.table-name', 'sent_emails'));
    }
}

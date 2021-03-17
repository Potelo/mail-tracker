<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use jdavidbakr\MailTracker\Model\SentEmail;

class CreateSentEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection((new SentEmail)->getConnectionName())->create(config('mail-tracker.table-name', 'sent_emails'), function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->char('hash',32)->unique();
            $table->text('headers')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('subject')->nullable();
            $table->integer('opens')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('user_id')->index()->nullable();
            $table->string('mailable')->nullable();
            $table->string('message_id')->nullable();
            $table->text('meta');

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
        Schema::connection((new SentEmail())->getConnectionName())->drop(config('mail-tracker.table-name', 'sent_emails'));
    }
}

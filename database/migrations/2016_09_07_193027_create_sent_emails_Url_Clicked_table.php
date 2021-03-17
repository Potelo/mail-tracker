<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use jdavidbakr\MailTracker\Model\SentEmailUrlClicked;

class CreateSentEmailsUrlClickedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection((new SentEmailUrlClicked())->getConnectionName())->create(config('mail-tracker.url-clicked-table-name', 'sent_emails_url_clicked'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sent_email_id')->unsigned();
            //$table->foreign('sent_email_id')->references('id')->on(config('mail-tracker.table-name', 'sent_emails'))->onDelete('cascade');
            $table->text('url')->nullable();
            $table->char('hash', 32);
            $table->integer('clicks')->default('1');
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
        Schema::connection((new SentEmailUrlClicked())->getConnectionName())->drop(config('mail-tracker.url-clicked-table-name', 'sent_emails_url_clicked'));
    }
}

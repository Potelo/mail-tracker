<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use jdavidbakr\MailTracker\Model\SentEmailContent;

class CreateSentEmailContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('mail-tracker.content-table-name', 'sent_emails_content'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sent_email_id')->unsigned()->index();
            $table->text('content')->nullable();
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
        Schema::dropIfExists('sent_emails_content');
    }
}

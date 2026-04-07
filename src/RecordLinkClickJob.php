<?php

namespace jdavidbakr\MailTracker;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use jdavidbakr\MailTracker\Events\LinkClickedEvent;

class RecordLinkClickJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $sentEmail;
    public $url;
    public $ipAddress;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    public function retryUntil()
    {
        return now()->addDays(5);
    }

    public function __construct($sentEmail, $url, $ipAddress)
    {
        $this->sentEmail = $sentEmail;
        $this->url = $url;
        $this->ipAddress = $ipAddress;
    }

    public function handle()
    {
        $this->sentEmail->increment('clicks');

        $url_clicked = MailTracker::sentEmailUrlClickedModel()->newQuery()->firstOrCreate(
            ['url' => $this->url, 'hash' => $this->sentEmail->hash],
            ['sent_email_id' => $this->sentEmail->id, 'clicks' => 0]
        );
        $url_clicked->increment('clicks');

        Event::dispatch(new LinkClickedEvent(
            $this->sentEmail,
            $this->ipAddress,
            $this->url,
        ));
    }
}

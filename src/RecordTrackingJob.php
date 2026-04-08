<?php

namespace jdavidbakr\MailTracker;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;
use jdavidbakr\MailTracker\Events\ViewEmailEvent;

class RecordTrackingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $sentEmail;
    public $ipAddress;
    public $openedAt = null;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    public function __construct($sentEmail, $ipAddress, $openedAt)
    {
        $this->sentEmail = $sentEmail;
        $this->ipAddress = $ipAddress;
        $this->openedAt = $openedAt;
    }

    public function retryUntil()
    {
        return now()->addDays(5);
    }

    public function handle()
    {
        $this->sentEmail->increment('opens');
        Event::dispatch(new ViewEmailEvent($this->sentEmail, $this->ipAddress));

        if (!empty($this->openedAt) && !$this->sentEmail->opened_at) {
            $this->sentEmail->opened_at = $this->openedAt;
            $this->sentEmail->save();
        }
    }
}

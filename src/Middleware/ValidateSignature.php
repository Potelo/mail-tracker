<?php

namespace jdavidbakr\MailTracker\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ValidateSignature as Middleware;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Uri;
use jdavidbakr\MailTracker\Events\ValidLinkEvent;
use jdavidbakr\MailTracker\Exceptions\BadUrlLink;
use jdavidbakr\MailTracker\MailTracker;
use jdavidbakr\MailTracker\Model\SentEmail;

class ValidateSignature extends Middleware
{
    public function handle($request, Closure $next, ...$args)
    {
        $hash = $request->query('h');
        $url  = $request->query('l');

        if (!$hash || !$url) {
            return redirect(config('mail-tracker.redirect-missing-links-to') ?: '/');
        }

        $uri = Uri::of($url);

        $validUriSchemes = ['http', 'https', 'tel', 'mailto'];

        if (!in_array($uri->scheme(), $validUriSchemes)) {
            throw new BadUrlLink('Mail hash: ' . $hash . ', URL: ' . $url);
        }

        [$relative, $ignore] = $this->parseArguments($args);

        // If the signature is valid then we know that it has not been tampered with so continue
        if ($request->hasValidSignatureWhileIgnoring($ignore, !$relative)) {
            return $next($request);
        }

        // If the signature is not valid then we need to check if the link is valid
        /** @var SentEmail $tracker */
        if ($tracker = MailTracker::sentEmailModel()->newQuery()->where('hash', $hash)->first()) {
            // If the link is not from a valid signed route then determine if the link is valid
            $event = new ValidLinkEvent($tracker, $url);

            Event::dispatch($event);

            if ($event->valid) {
                return $next($request);
            }
        }

        return redirect(config('mail-tracker.redirect-missing-links-to') ?: '/');
    }
}

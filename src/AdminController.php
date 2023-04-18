<?php

namespace jdavidbakr\MailTracker;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    /**
     * Sent email search
     */
    public function postSearch(Request $request)
    {
        session(['mail-tracker-index-search' => $request->search]);
        return redirect(route('mailTracker_Index'));
    }

    /**
     * Clear search
     */
    public function clearSearch()
    {
        session(['mail-tracker-index-search' => null]);
        return redirect(route('mailTracker_Index'));
    }

    /**
     * Index.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        session(['mail-tracker-index-page' => request()->page]);
        $search = session('mail-tracker-index-search');

        $query = MailTracker::sentEmailModel()->query();

        if($search) {
            $terms = explode(" ",$search);
            foreach($terms as $term) {
                $query->where(function($q) use($term) {
                    $q->where('sender_name','like','%'.$term.'%')
                        ->orWhere('sender_email','like','%'.$term.'%')
                        ->orWhere('recipient_name','like','%'.$term.'%')
                        ->orWhere('recipient_email','like','%'.$term.'%')
                        ->orWhere('subject','like','%'.$term.'%');
                });
            }
        }
        $query->orderBy('created_at', 'desc');

        $emails = $query->cursorPaginate(config('mail-tracker.emails-per-page'));

        return \View('emailTrakingViews::index')->with('emails', $emails);
    }

    /**
     * Show Email.
     *
     * @return \Illuminate\Http\Response
     */
    public function getShowEmail($id)
    {
        $email = MailTracker::sentEmailModel()->newQuery()->where('id', $id)->first();
        return \View('emailTrakingViews::show')->with('email', $email);
    }

    /**
     * Url Detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUrlDetail($id)
    {
        $details = MailTracker::sentEmailUrlClickedModel()->newQuery()->where('sent_email_id', $id)
            ->with('email')
            ->get();
        if (!$details) {
            return back();
        }
        return \View('emailTrakingViews::url_detail')->with('details', $details);
    }

    /**
     * SMTP Detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSMTPDetail($id)
    {
        $details = MailTracker::sentEmailModel()->newQuery()->find($id);
        if (!$details) {
            return back();
        }
        return \View('emailTrakingViews::smtp_detail')->with('details', $details);
    }
}

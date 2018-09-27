<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Pageview;
use Illuminate\Http\Request;

class PageviewController extends Controller
{
    public function store(Site $site, Request $request)
    {
        if ($this->shouldTrack($request)) {
            $this->buildHistory($request, Pageview::fromRequest($request, $site));
        }

        return response(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
                        ->header('Tk', 'N')
                        ->header('Content-Type', 'image/gif')
                        ->header('Expires', 'Mon, 01 Jan 1990 00:00:00 GMT')
                        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                        ->header('Pragma', 'no-cache');
    }

    public function shouldTrack(Request $request)
    {
        if ($request->header('DNT', '0') === '1') {
            return false;
        }

        if ($request->header('X-Moz') == 'prefetch' || $request->header('X-Purpose') == 'preview') {
            return false;
        }

        if (isBot($request->userAgent())) {
            return false;
        }

        return true;
    }

    protected function buildHistory(Request $request, Pageview $pageview)
    {
        if ($pageview->isNewSession() || ! $request->has('pid') || $request->input('pid') == '') {
            return;
        }

        $previous = Pageview::find($request->input('pid'));

        if (is_null($previous) || $previous->minutesHavePassed(30)) {
            return;
        }

        $previous->update([
            'duration' => $pageview->visited_at->timestamp - $previous->visited_at->timestamp,
            'bounce'   => false
        ]);
    }
}

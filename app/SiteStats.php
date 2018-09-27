<?php

namespace App;

use App\Site;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsStatsModel;

class SiteStats extends Model
{
    use IsStatsModel;

    protected function computeStatsFor(Pageview $pageview)
    {
        $this->increment('pageviews');

        if ($pageview->duration > 0) {
            $this->increment('known_durations');
            $this->avg_duration = $this->avg_duration + (((float) $pageview->duration) - $this->avg_duration) * 1 / ((float) $this->known_durations);
        }

        if ($pageview->isNewVisitor()) {
            $this->increment('visitors');
        }

        if ($pageview->isNewSession()) {
            $this->increment('sessions');

            if ($pageview->isBounce()) {
                $this->bounce_rate = ((((float) $this->sessions -1) * $this->bounce_rate) + 1) / ((float) $this->sessions);
            } else {
                $this->bounce_rate = ((((float) $this->sessions -1) * $this->bounce_rate) + 0) / ((float) $this->sessions);
            }
        }
    }
}

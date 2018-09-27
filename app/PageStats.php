<?php

namespace App;

use App\Site;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsStatsModel;

class PageStats extends Model
{
    use IsStatsModel;

    protected function computeStatsFor(Pageview $pageview)
    {
        $this->increment('pageviews');

        if ($pageview->unique) {
            $this->increment('visitors');
        }

        if ($pageview->duration > 0) {
            $this->increment('known_durations');
            $this->avg_duration = $this->avg_duration + (((float) $pageview->duration) - $this->avg_duration) * 1 / ((float) $this->known_durations);
        }

        if ($pageview->isNewSession()) {
            $this->increment('entries');

            if ($pageview->isBounce()) {
                $this->bounce_rate = ((((float) $this->entries -1) * $this->bounce_rate) + 1) / ((float) $this->entries);
            } else {
                $this->bounce_rate = ((((float) $this->entries -1) * $this->bounce_rate) + 0) / ((float) $this->entries);
            }
        }
    }
}

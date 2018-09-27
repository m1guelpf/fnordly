<?php

namespace App;

use App\Site;
use App\Pageview;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsStatsModel;

class RefererStats extends Model
{
    use IsStatsModel;

    public static function process(Pageview $pageview, Site $site)
    {
        [$host, $path] = $pageview->parseReferer();

        return static::firstOrNew(['date' => $pageview->date, 'site_id' => $site->id, 'host' => $host, 'path' => $path])->add($pageview);
    }

    protected function computeStatsFor(Pageview $pageview)
    {
        $this->increment('pageviews');

        if ($pageview->isNewVisitor()) {
            $this->increment('visitors');
        }

        if ($pageview->isBounce()) {
            $this->bounce_rate = ((((float) $this->pageviews -1) * $this->bounce_rate) + 1) / ((float) $this->pageviews);
        } else {
            $this->bounce_rate = ((((float) $this->pageviews -1) * $this->bounce_rate) + 0) / ((float) $this->pageviews);
        }

        if ($pageview->duration > 0) {
            $this->increment('known_durations');
            $this->avg_duration = $this->avg_duration + (((float) $pageview->duration) - $this->avg_duration) * 1 / ((float) $this->known_durations);
        }
    }
}

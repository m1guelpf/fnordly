<?php

namespace App\Models\Stats;

use App\Models\Site;
use App\Models\Pageview;
use App\Traits\IsStatsModel;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class DeviceStats extends Model
{
    use IsStatsModel;

    protected function computeStatsFor(Pageview $pageview)
    {
        $this->increment('pageviews');

        $browser = $pageview->getBrowser();

        foreach (['desktop', 'mobile', 'tablet'] as $device) {
            if (! $browser->getType($device)) {
                continue;
            }

            return $this->increment($device);
        }

        $this->increment('other');
    }
}

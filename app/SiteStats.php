<?php

namespace App;

use App\Site;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class SiteStats extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'date';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['site'];

    public static function generateStats()
    {
        foreach ($this->site()->pageviews()->where('visited_at', $this->date)->get() as $pageview) {
            $this->computeStatsFor($pageview);
        }
    }

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

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

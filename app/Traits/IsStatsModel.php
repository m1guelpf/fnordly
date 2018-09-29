<?php

namespace App\Traits;

use App\Models\Site;
use App\Models\Pageview;

trait IsStatsModel
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

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

    public function process(Pageview $pageview, Site $site)
    {
        return static::firstOrNew(['date' => $pageview->date, 'site_id' => $site->id])->add($pageview);
    }

    public function add(Pageview $pageview)
    {
        $this->computeStatsFor($pageview);

        $this->save();
    }

    abstract protected function computeStatsFor(Pageview $pageview);

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

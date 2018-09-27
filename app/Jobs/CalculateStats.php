<?php

namespace App\Jobs;

use App\Site;
use App\Pageview;
use App\PageStats;
use App\SiteStats;
use App\RefererStats;
use Illuminate\Bus\Queueable;
use Illuminated\Console\Mutex;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminated\Console\WithoutOverlapping;

class CalculateStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WithoutOverlapping;

    protected $site;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mutex = new Mutex($this);

        if (!$mutex->acquireLock($this->getMutexTimeout())) {
            $this->release(10);
        }

        try {
            $this->computeStats();
        } catch (\Throwable $t) {
            $this->releaseMutexLock();

            throw $t;
        }

        $site->views()->delete();

        $this->releaseMutexLock();
    }

    protected function computeStats()
    {
        foreach ($this->getPageviews() as $pageview) {
            SiteStats::process($pageview, $this->site);
            PageStats::process($pageview, $this->site);

            if ($pageview->hasReferer()) {
                [$host, $path] = $pageview->parseReferer();
                RefererStats::firstOrNew(['date' => $date, 'site_id' => $this->site->id, 'host' => $host, 'path' => $path])->add($pageview);
            }
        }
        foreach ($this->getAvailableDates() as $date) {
            DeviceStats::firstOrNew(['date' => $date, 'site_id' => $this->site->id])->generateStats();
        }
    }

    protected function getPageviews()
    {
        return $this->site->views()->all();
    }
}

<?php

namespace App\Jobs;

use App\Models\Site;
use App\Models\Pageview;
use App\Models\Stats\PageStats;
use App\Models\Stats\SiteStats;
use App\Models\Stats\RefererStats;
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
            DeviceStats::process($pageview, $this->site);

            if ($pageview->hasReferer()) {
                [$host, $path] = $pageview->parseReferer();
                RefererStats::firstOrNew(['date' => $date, 'site_id' => $this->site->id, 'host' => $host, 'path' => $path])->add($pageview);
            }
        }
    }

    protected function getPageviews()
    {
        return $this->site->views()->all();
    }
}

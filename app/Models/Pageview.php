<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pageview extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'newVisitor' => 'boolean',
        'newSession' => 'boolean',
        'unique'     => 'boolean',
        'bounce'     => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['visited_at'];

    public static function fromRequest(Request $request, Site $site) : self
    {
        return static::create([
                'id'           =>   $request->input('id'),
                'site_id'      =>   $site->id,
                'host'         =>   parseHost($request->input('h')),
                'path'         =>   Str::start($request->input('p'), '/'),
                'newVisitor'   =>   $request->input('nv') == '1',
                'newSession'   =>   $request->input('ns') == '1',
                'unique'       =>   $request->input('u') == '1',
                'bounce'       =>   $request->input('b') != '0',
                'referer'      =>   parseReferrer($request->input('r')),
                'user_agent'   =>   $request->userAgent(),
                'duration'     =>   0,
                'visited_at'    =>   now(),
            ]);
    }

    public function isNewSession() : bool
    {
        return $this->newSession;
    }

    public function isNewVisitor() : bool
    {
        return $this->newVisitor;
    }

    public function isBounce() : bool
    {
        return $this->bounce;
    }

    public function hasReferer() : bool
    {
        return $this->referer != '';
    }

    public function parseReferer() : array
    {
        return [parseHost($this->referer), parse_url($this->referer, PHP_URL_PATH)];
    }

    public function minutesHavePassed(int $minutes) : bool
    {
        return $this->visited_at->diffInMinutes($this->visited_at->copy()->addMinutes($minutes)) >= $minutes;
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Scope a query to only include real-time views.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRealtime($query)
    {
        return $query->where(function ($query) {
            $query->where('duration', 0)->orWhere('bounce', true);
        })->where('timestamp', '>', now()->addMinutes(-5)->timestamp);
    }
}

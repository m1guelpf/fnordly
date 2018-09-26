<?php

namespace App;

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
                'width'        =>   $request->input('vw'),
                'height'       =>   $request->input('vh'),
                'referer'      =>   parseReferrer($request->input('r')),
                'duration'     =>   0,
                'visited_at'    =>   now(),
            ]);
    }

    public function isNewSession()
    {
        return $this->newSession;
    }

    public function minutesHavePassed(int $minutes) : bool
    {
        return $this->visited_at->diffInMinutes($this->visited_at->copy()->addMinutes($minutes)) >= $minutes;
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

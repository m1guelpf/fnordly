<?php

namespace App\Http\Controllers\Api;

use App\Jobs\CalculateStats;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SiteStatsController extends Controller
{
    public function __handle(Request $request)
    {
        $request->validate([
            'date' => 'required,date'
        ]);

        try {
            return SiteStats::whereDate('date', $date = Carbon::parse($request->date))->firstOrFail();
        } catch (ModelNotFoundException $e) {
            CalculateStats::dispatch($site);

            return response()->json(['message' => __('No data yet')], 102);
        }
    }
}

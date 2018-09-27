<?php

namespace App\Http\Controllers\Api;

use App\Model\Pageview;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RealTimeController extends Controller
{
    public function __handle()
    {
        return Pageview::realtime()->count();
    }
}

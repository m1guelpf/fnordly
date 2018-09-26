<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MatthiasMullie\Minify\JS as Minifier;

class TrackerController extends Controller
{
    public function __invoke()
    {
        return response((new Minifier(base_path('resources/js/tracker.js')))->minify())
                        ->header('Content-Type', 'application/javascript')
                        ->header('Tk', 'N');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Court;

class HomeController extends Controller
{
    public function index()
    {
        $courts = Court::where('is_available', true)->take(4)->get();
        return view('home', compact('courts'));
    }
}

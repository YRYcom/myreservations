<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class homePage extends Controller
{
    public function index()
    {
        return view('home');
    }
}

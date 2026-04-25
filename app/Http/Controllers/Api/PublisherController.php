<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publisher;

class PublisherController extends Controller
{
    public function index()
    {
        return Publisher::with('volumes')->get();
    }
}
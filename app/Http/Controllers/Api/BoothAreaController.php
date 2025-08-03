<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoothArea;
use Illuminate\Http\Request;

class BoothAreaController extends Controller
{
    //
    public function index()
    {
        return BoothArea::all();
    }

}

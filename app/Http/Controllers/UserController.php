<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Carbon\Carbon;


class UserController extends Controller
{


    public function index()
    {
        $rates = Booking::all();

        //return view('ra.index', compact('rates'));
        return response()->json($rates);
    }



}

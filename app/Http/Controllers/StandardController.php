<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Standard;

class StandardController extends Controller
{
    public function getBySection(Request $request)
    {
        $standards = Standard::where('section_id', $request->section_id)->get();
        return response()->json($standards);
    }
}

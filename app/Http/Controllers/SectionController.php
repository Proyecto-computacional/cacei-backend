<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
class SectionController extends Controller
{
    public function getByCategory(Request $request)
    {
        $sections = Section::where('category_id', $request->category_id)->get();
        return response()->json($sections);
    }
}

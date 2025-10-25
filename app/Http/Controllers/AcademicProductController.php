<?php

namespace App\Http\Controllers;

use App\Models\AcademicProduct;
use Illuminate\Http\Request;

class AcademicProductController extends Controller
{
    public function index($cv_id)
    {
        $academicProducts = AcademicProduct::where('cv_id', $cv_id)->get();
        return response()->json($academicProducts);
    }

    public function store(Request $request, $cv_id)
{
    $validated = $request->validate([
        'description' => 'required|string|max:150',
    ]);

    $academicProduct = AcademicProduct::updateOrCreate(
        [
            'cv_id' => $cv_id,
            'academic_product_id' => $request->input('academic_product_id')
        ],
        $validated
    );

    return response()->json($academicProduct, 201);
}


    public function show($cv_id, $academic_product_id)
    {
        $academicProduct = AcademicProduct::where('cv_id', $cv_id)->findOrFail($academic_product_id);
        return response()->json($academicProduct);
    }

    public function update(Request $request, $cv_id, $academic_product_id)
    {
        $academicProduct = AcademicProduct::where('cv_id', $cv_id)->findOrFail($academic_product_id);
        $academicProduct->update($request->all());

        return response()->json($academicProduct);
    }

    public function destroy($cv_id, $academic_product_id)
    {
        $academicProduct = AcademicProduct::where('cv_id', $cv_id)->findOrFail($academic_product_id);
        $academicProduct->delete();

        return response()->json(null, 204);
    }
}

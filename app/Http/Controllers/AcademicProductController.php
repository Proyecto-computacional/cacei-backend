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

        // Verificar si ya existe un producto idéntico
        $existingProduct = AcademicProduct::where('cv_id', $cv_id)
            ->where('description', $validated['description'])
            ->first();
            error_log($existingProduct);
        if ($existingProduct) {
            return response()->json([
                'message' => 'Datos actualizados',
                'data' => $existingProduct
            ], 409); // Código 409 = Conflicto
        }

        // Asignar número secuencial único solo para nuevos registros
        $lastProductNumber = AcademicProduct::where('cv_id', $cv_id)
            ->max('academic_product_number') ?? 0;

        $academicProduct = AcademicProduct::create([
            'cv_id' => $cv_id,
            'academic_product_number' => $lastProductNumber + 1,
            'description' => $validated['description']
        ]);

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

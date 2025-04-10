<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

    public function getByFrame(Request $request)
    {
        $categories = Category::where('frame_id', $request->frame_id)->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'frame_id' => 'required|int',
            'category_name' => 'required|string|max:50'
        ]);
        
        // Generar un ID único
        do {
            $randomId = rand(1, 100);
        } while (Category::where('category_id', $randomId)->exists()); // Verifica que no se repita

        $category = new Category();
        $category->category_id = $randomId;
        $category->frame_id = $request->input('frame_id');
        $category->category_name = $request->input('category_name');

        $category->save();

        return response()->json([
            'message' => 'Registro creado correctamente',
            'data' => $category
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'category_id' => 'required|int',
            'category_name' => 'required|string|max:50'
        ]);

        $category = Category::find($request->category_id);

        if (!$category) {
            return response()->json([
                'message' => 'Registro no encontrado.'
            ], 404);
        }

        $category->category_name = $request->input('category_name');

        $category->save();

        return response()->json([
            'message' => 'Registro actualizado correctamente.',
            'data' => $category
        ]);
    }
}

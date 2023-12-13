<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        // Solo trae las categorías que no están inactivas
        $categories = Category::where('status', '<>', 'Inactivo')->get();
        return response()->json(['categories' => $categories]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Activo,Inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = Category::create($validator->validated());
        return response()->json(['category' => $category, 'message' => 'Categoría creada exitosamente'], 201);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        return response()->json(['category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:Activo,Inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category->update($validator->validated());
        return response()->json(['category' => $category, 'message' => 'Categoría actualizada exitosamente']);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        // Marcar la categoría como inactiva en lugar de eliminarla
        $category->status = 'Inactivo';
        $category->save();

        return response()->json(['message' => 'Categoría marcada como inactiva']);
    }
    
}

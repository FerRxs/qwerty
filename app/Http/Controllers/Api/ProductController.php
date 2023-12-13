<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all()->map(function ($product) {
            return $this->formatProduct($product);
        });
        return response()->json(['products' => $products]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Activo,Inactivo,Descontinuado',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();
        $product = Product::create($data);

        // Guardar imágenes después de obtener el ID del producto
        $product->image1 = $this->handleImage($request->file('image1'), $product, 1);
        $product->image2 = $this->handleImage($request->file('image2'), $product, 2);
        $product->image3 = $this->handleImage($request->file('image3'), $product, 3);
        $product->save();

        return response()->json(['product' => $this->formatProduct($product), 'message' => 'Producto creado exitosamente'], 201);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return response()->json(['product' => $this->formatProduct($product)]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'exists:categories,id',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'in:Activo,Inactivo,Descontinuado',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();
        $product->update($data);

        // Actualizar imágenes
        $product->image1 = $this->handleImage($request->file('image1'), $product, 1);
        $product->image2 = $this->handleImage($request->file('image2'), $product, 2);
        $product->image3 = $this->handleImage($request->file('image3'), $product, 3);
        $product->save();

        return response()->json(['product' => $this->formatProduct($product), 'message' => 'Producto actualizado exitosamente']);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Eliminar imágenes si existen
        $this->deleteImage($product->image1);
        $this->deleteImage($product->image2);
        $this->deleteImage($product->image3);

        $product->delete();
        return response()->json(['message' => 'Producto eliminado exitosamente']);
    }

    private function handleImage($file, $product, $imageNumber)
{
    if ($file) {
        $fileName = $this->generateImageName($file, $product, $imageNumber);
        return $file->storeAs('images/products', $fileName, 'public');
    }
    return $product["image{$imageNumber}"];
}


    private function generateImageName($file, $product, $imageNumber)
{
    if (!$file) {
        return null;
    }
    
    $sanitizedProductName = Str::slug($product->name);
    return "{$sanitizedProductName}_{$product->id}_img{$imageNumber}." . $file->getClientOriginalExtension();
}


    private function deleteImage($imagePath)
    {
        if ($imagePath && Storage::exists($imagePath)) {
            Storage::delete($imagePath);
        }
    }

    private function formatProduct($product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'category_id' => $product->category_id,
            'status' => $product->status,
            'image1_url' => $product->image1 ? Storage::url($product->image1) : null,
            'image2_url' => $product->image2 ? Storage::url($product->image2) : null,
            'image3_url' => $product->image3 ? Storage::url($product->image3) : null,
        ];
    }
}

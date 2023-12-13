<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class CartController extends Controller
{
    // Asegúrate de que el usuario esté autenticado para todas las operaciones
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        // Obtener el carrito del usuario autenticado
        $cartItems = Cart::session($request->user()->id)->getContent();
        return response()->json(['cartItems' => $cartItems]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::find($request->id);
        if (!$product || $product->stock < $request->quantity) {
            return response()->json(['error' => 'Producto no disponible en la cantidad deseada.'], 400);
        }

        // Vincular el carrito con el usuario autenticado y agregar el producto
        Cart::session($request->user()->id)->add($request->id, $product->name, $product->price, $request->quantity, array());
        return response()->json(['message' => 'Producto añadido al carrito correctamente.']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if (!Cart::session($request->user()->id)->get($id)) {
            return response()->json(['error' => 'Producto no encontrado en el carrito.'], 404);
        }

        // Actualizar la cantidad del producto en el carrito
        Cart::session($request->user()->id)->update($id, [
            'quantity' => [
                'relative' => false,
                'value' => $request->quantity
            ],
        ]);

        return response()->json(['message' => 'Carrito actualizado correctamente.']);
    }

    public function remove(Request $request, $id)
    {
        if (!Cart::session($request->user()->id)->get($id)) {
            return response()->json(['error' => 'Producto no encontrado en el carrito.'], 404);
        }

        // Eliminar el producto del carrito
        Cart::session($request->user()->id)->remove($id);
        return response()->json(['message' => 'Producto eliminado del carrito correctamente.']);
    }

    public function clear(Request $request)
    {
        // Vaciar el carrito
        Cart::session($request->user()->id)->clear();
        return response()->json(['message' => 'Carrito vaciado correctamente.']);
    }

    public function count(Request $request)
    {
        // Obtener la cantidad de productos en el carrito
        return response()->json(['count' => Cart::session($request->user()->id)->getContent()->count()]);
    }
}

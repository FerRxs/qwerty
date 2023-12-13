<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderDetails.product')->get();
        return response()->json(['orders' => $orders]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:Pendiente,Enviado,Entregado,Cancelado',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $order = Order::create($validator->validated());
        return response()->json(['order' => $order, 'message' => 'Orden creada exitosamente'], 201);
    }

    public function show($id)
    {
        $order = Order::with('orderDetails.product')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        return response()->json(['order' => $order]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id',
            'date' => 'date',
            'total' => 'numeric|min:0',
            'status' => 'in:Pendiente,Enviado,Entregado,Cancelado',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $order->update($validator->validated());
        return response()->json(['order' => $order, 'message' => 'Orden actualizada exitosamente']);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['message' => 'Orden no encontrada'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'Orden eliminada exitosamente']);
    }
}

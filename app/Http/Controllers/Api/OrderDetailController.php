<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderDetailController extends Controller
{
    public function index()
    {
        $orderDetails = OrderDetail::with('order', 'product')->get();
        return response()->json(['orderDetails' => $orderDetails]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'status' => 'required|in:Activo,Inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $orderDetail = OrderDetail::create($validator->validated());
        return response()->json(['orderDetail' => $orderDetail, 'message' => 'Detalle de orden creado exitosamente'], 201);
    }

    public function show($id)
    {
        $orderDetail = OrderDetail::with('order', 'product')->find($id);

        if (!$orderDetail) {
            return response()->json(['message' => 'Detalle de orden no encontrado'], 404);
        }

        return response()->json(['orderDetail' => $orderDetail]);
    }

    public function update(Request $request, $id)
    {
        $orderDetail = OrderDetail::find($id);

        if (!$orderDetail) {
            return response()->json(['message' => 'Detalle de orden no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'integer|min:1',
            'unit_price' => 'numeric|min:0',
            'subtotal' => 'numeric|min:0',
            'status' => 'in:Activo,Inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $orderDetail->update($validator->validated());
        return response()->json(['orderDetail' => $orderDetail, 'message' => 'Detalle de orden actualizado exitosamente']);
    }

    public function destroy($id)
    {
        $orderDetail = OrderDetail::find($id);

        if (!$orderDetail) {
            return response()->json(['message' => 'Detalle de orden no encontrado'], 404);
        }

        $orderDetail->delete();
        return response()->json(['message' => 'Detalle de orden eliminado exitosamente']);
    }
}

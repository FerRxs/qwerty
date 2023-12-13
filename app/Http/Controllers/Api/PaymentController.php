<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\Order;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class PaymentController extends Controller
{
    public function charge(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'No estás autenticado.'], 401);
        }

        $cartItems = Cart::getContent();
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Tu carrito está vacío.'], 400);
        }

        $stripeToken = $request->input('stripeToken');
        if (!$stripeToken) {
            return response()->json(['error' => 'El token de Stripe no se recibió correctamente.'], 400);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $charge = Charge::create([
                'amount' => round(Cart::getTotal() * 100),
                'currency' => 'usd',
                'description' => 'Pago de pedido',
                'source' => $stripeToken,
            ]);

            if ($charge->status == 'succeeded') {
                $order = $this->createOrder($user, $cartItems);

                Cart::clear();
                return response()->json(['order' => $order, 'message' => 'Tu pedido ha sido procesado y el pago fue exitoso.']);
            } else {
                return response()->json(['error' => 'El pago no fue exitoso.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar el pago: ' . $e->getMessage()], 500);
        }
    }

    private function createOrder($user, $cartItems)
    {
        $order = $user->orders()->create([
            'date' => now(),
            'total' => Cart::getTotal(),
            'status' => 'Pagado',
        ]);

        foreach ($cartItems as $item) {
            $order->orderDetails()->create([
                'product_id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'subtotal' => $item->getPriceSum(),
            ]);
        }

        return $order;
    }
}

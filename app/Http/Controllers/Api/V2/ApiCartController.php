<?php

// app/Http/Controllers/ApiCartController.php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ApiCartController extends Controller
{
    // GET /api/cart
    public function current(Request $request)
    {
        $cart = Cart::firstOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'open']
        );

        return $cart->load('items.product');
    }

    // POST /api/cart/items
    public function addItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'options'    => 'array|nullable',
        ]);

        $cart = Cart::firstOrCreate(
            ['user_id' => $request->user()->id, 'status' => 'open']
        );

        $product = Product::findOrFail($data['product_id']);

        $item = $cart->items()
            ->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity'   => $data['quantity'],
                    'unit_price' => $product->price,
                    'options'    => $data['options'] ?? [],
                ]
            );

        return $item->load('product');
    }

    // PUT /api/cart/items/{item}
    public function updateItem(Request $request, CartItem $item)
    {
        $data = $request->validate([
            'quantity' => 'integer|min:1',
            'options'  => 'array|nullable',
        ]);

        $item->update($data);
        return $item->load('product');
    }

    // DELETE /api/cart/items/{item}
    public function removeItem(CartItem $item)
    {
        $item->delete();
        return response()->noContent();
    }
}

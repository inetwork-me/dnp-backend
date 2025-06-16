<?php

// app/Http/Controllers/WebsiteCartController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class WebsiteCartController extends Controller
{

    /** GET /api/cart **/
    public function current(Request $request)
    {
        // allow user_id to be null
        $userId = optional($request->user())->id;

        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
            'status'  => 'open',
        ]);

        return response()->json(
            $cart->load('items.product')
        );
    }

    /** POST /api/cart/items **/
    public function addItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'options'    => 'array|nullable',
        ]);

        $userId = optional($request->user())->id;
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
            'status'  => 'open',
        ]);

        $product = Product::findOrFail($data['product_id']);

        $item = $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'quantity'   => $data['quantity'],
                'unit_price' => $product->unit_price,
                'options'    => $data['options'] ?? [],
            ]
        );

        return response()->json($item->load('product'));
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

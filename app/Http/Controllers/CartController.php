<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    public function newCart()
    {
        //check if user has a cart don't create a new one
        if (auth()->user()->cart()) {
            return response()->json(['error' => 'User already has a cart'], 404);
        } else {
            $cart = Cart::create([
                'user_id' => auth()->user()->id,
            ]);
            return response()->json(['cart' => $cart]);
        }
    }

    public function addCartItem(Request $request)
    {
        // Get the user's cart with its items and related products
        $cart = auth()->user()->cart()->with('cartItems.product')->first();

        if (!$cart) {
            return response()->json(['error' => 'User does not have a cart'], 404);
        }

        // Get the product from the request
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Check if the product exists and has sufficient quantity
        $product = $cart->cartItems->where('product_id', $productId)->first();
        if (!$product || $product->product->quantity < $quantity) {
            return response()->json(['error' => 'Invalid product or insufficient quantity'], 422);
        }

        // Update product quantity
        $product->product->decrement('quantity', $quantity);

        // Add new cart item using cart relationship
        $cartItem = $cart->cartItems()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);

        return response()->json(['cartItem' => $cartItem]);
    }


    //list all products in all carts
    public function listProductsInCart()
    {
        $cartItems = CartItem::all();
        return response()->json(['cartItems' => $cartItems]);
    }

    //get specific cart details
    public function getCartDetails()
    {
        // Get the user's cart with its items and related products
        $cart = auth()->user()->cart()->with('cartItems.product')->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json(['cart' => $cart]);
    }

    //delete cart item
    public function deleteCartItem($id)
{
    // Get the cart item with its related product
    $cartItem = CartItem::with('product')->find($id);

    // Check if the cart item exists
    if (!$cartItem) {
        return response()->json(['error' => 'Cart item not found'], 404);
    }

    // Update product quantity
    $cartItem->product->increment('quantity', $cartItem->quantity);

    // Delete cart item
    $cartItem->delete();

    return response()->json(['message' => 'Cart item deleted successfully'], 200);
}


    //update cart item quantity
    public function updateCartItem(Request $request, $id)
{
    // Get the cart item with its related product
    $cartItem = CartItem::with('product')->find($id);

    // Check if the cart item exists
    if (!$cartItem) {
        return response()->json(['error' => 'Cart item not found'], 404);
    }

    // Check if product quantity is sufficient
    $requestedQuantity = $request->input('quantity');
    if ($cartItem->product->quantity < $requestedQuantity) {
        return response()->json(['error' => 'Product quantity is less than the quantity requested'], 422);
    }

    // Update product quantity
    $cartItem->product->decrement('quantity', $requestedQuantity);

    // Update cart item quantity
    $cartItem->quantity = $requestedQuantity;
    $cartItem->save();

    return response()->json(['message' => 'Cart item updated successfully'], 200);
}


    //checkout
    public function checkout($address_id)
{
    // Get the user's cart with its items and related products
    $cart = auth()->user()->cart()->with('cartItems.product')->first();

    if (!$cart) {
        return response()->json(['error' => 'User does not have a cart'], 404);
    }

    // Calculate total price and gather products
    $totalPrice = 0;
    $products = [];

    foreach ($cart->cartItems as $cartItem) {
        $product = $cartItem->product;
        $totalPrice += $product->price * $cartItem->quantity;
        $products[] = $product;
    }

    // Delete cart items
    $cart->cartItems()->delete();

    // Make a new order and add order items
    $order = Order::create([
        'user_id' => auth()->user()->id,
        'address_id' => $address_id,
        'total' => $totalPrice,
        'status' => 'processing',
    ]);

    foreach ($cart->cartItems as $cartItem) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $cartItem->product->id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->product->price * $cartItem->quantity,
        ]);
    }

    return response()->json([
        'message' => 'Checkout successful', 'orderItems' => $order->orderItems
    ], 200);
}

}

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
        //add new cart using user id
        //check if user has a cart don't create a new one
        if (Cart::where('user_id', auth()->user()->id)->exists()) {
            return response()->json(['error' => 'User already has a cart'], 404);
        }
        else{
            $cart = Cart::create([
                'user_id' => auth()->user()->id,
            ]);
            return response()->json(['cart' => $cart]);
        }
    }

    public function addCartItem(Request $request)
    {
        //get cart id from user id
        $cart = Cart::where('user_id', auth()->user()->id)->first();
        //get the product to check quantity
        $product = Product::find($request->input('product_id'));
        //check if product quantity is less than the quantity requested
        if ($product->quantity < $request->input('quantity')) {
            return response()->json(['error' => 'Product quantity is less than the quantity requested'], 404);
        } else {
            //update product quantity
            $product->quantity = $product->quantity - $request->input('quantity');
            $product->save();
            //add new cart item using cart id and product id
            $cartItem = CartItem::create([
                //use cart id from cart table
                'cart_id' => $cart->id,
                'product_id' => $request->input('product_id'),
                'quantity' => $request->input('quantity'),
            ]);
            return response()->json(['cartItem' => $cartItem]);
        }
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
        //get cart details
        $cart = Cart::where('user_id', auth()->user()->id)->first();
        //get cart items
        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        //get products
        $products = [];
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            array_push($products, $product);
        }
        return response()->json(['cart' => $cart, 'cartItems' => $cartItems, 'products' => $products]);
    }

    //delete cart item
    public function deleteCartItem($id)
    {
        //get cart item
        $cartItem = CartItem::find($id);
        //get product
        $product = Product::find($cartItem->product_id);
        //update product quantity
        $product->quantity = $product->quantity + $cartItem->quantity;
        $product->save();
        //delete cart item
        $cartItem->delete();
        return response()->json(['message' => 'Cart item deleted successfully'], 200);
    }

    //update cart item quantity
    public function updateCartItem(Request $request, $id)
    {
        //get cart item
        $cartItem = CartItem::find($id);
        //get product
        $product = Product::find($cartItem->product_id);
        //check if product quantity is less than the quantity requested
        if ($product->quantity < $request->input('quantity')) {
            return response()->json(['error' => 'Product quantity is less than the quantity requested'], 404);
        } else {
            //update product quantity
            $product->quantity = $product->quantity - $request->input('quantity');
            $product->save();
            //update cart item quantity
            $cartItem->quantity = $request->input('quantity');
            $cartItem->save();
            return response()->json(['message' => 'Cart item updated successfully'], 200);
        }
    }

    
    //checkout
    public function checkout($address_id)
    {
        //get cart details
        $cart = Cart::where('user_id', auth()->user()->id)->first();
        //get cart items
        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        //get products
        $products = [];
        $totalPrice = 0;
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);
            $totalPrice = $totalPrice + ($product->price * $cartItem->quantity);
            array_push($products, $product);
        }
        //delete cart items
        foreach ($cartItems as $cartItem) {
            $cartItem->delete();
        }
        //make new order and add order items
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'address_id' => $address_id,
            'total' => $totalPrice,
            'status' => 'processing',
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $cartItem->quantity,
                'price' => $product->price * $cartItem->quantity,
            ]);
        }
        return response()->json(['message' => 'Checkout successful'
       , 'orderItems' => $order->orderItems
    ], 200);
    }
   
}

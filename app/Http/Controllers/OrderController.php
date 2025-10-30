<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Book;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ✅ إنشاء طلب شراء
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        // ممنوع المستخدم يشتري كتاب هو اللي أضافه
        if ($book->user_id === $request->user()->id) {
            return response()->json(['message' => 'You cannot buy your own book'], 403);
        }

        // ممنوع يطلب نفس الكتاب مرتين
        $existingOrder = Order::where('book_id', $book->id)
            ->where('buyer_id', $request->user()->id)
            ->where('status', 'pending')
            ->first();

        if ($existingOrder) {
            return response()->json(['message' => 'You already requested this book'], 400);
        }

        $order = Order::create([
            'book_id' => $book->id,
            'buyer_id' => $request->user()->id,
            'seller_id' => $book->user_id,
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order,
        ], 201);
    }

    // ✅ عرض الطلبات اللي أنا اشتريتها
    public function myOrders(Request $request)
    {
        $orders = $request->user()->boughtOrders()->with('book', 'seller')->get();

        return response()->json([
            'message' => 'Your orders retrieved successfully',
            'data' => $orders
        ]);
    }

    // ✅ عرض الطلبات اللي جاتلي كبائع
    public function receivedOrders(Request $request)
    {
        $orders = $request->user()->soldOrders()->with('book', 'buyer')->get();

        return response()->json([
            'message' => 'Received orders retrieved successfully',
            'data' => $orders
        ]);
    }

    // ✅ البائع يقبل أو يرفض الطلب
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $order = Order::where('seller_id', $request->user()->id)->findOrFail($id);

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
}

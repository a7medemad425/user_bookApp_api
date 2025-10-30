<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // ✅ عرض كل الكتب
    public function index()
    {
        $books = Book::with('user:id,name,email')->get();

        return response()->json([
            'message' => 'Books retrieved successfully',
            'data' => $books
        ]);
    }

    // ✅ عرض الكتب الخاصة بالمستخدم الحالي
    public function myBooks(Request $request)
    {
        $books = $request->user()->books;

        return response()->json([
            'message' => 'Your books retrieved successfully',
            'data' => $books
        ]);
    }

    // ✅ إضافة كتاب جديد
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books', 'public');
        }

        $book = Book::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'author' => $request->author,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Book added successfully',
            'data' => $book
        ], 201);
    }

    // ✅ تحديث بيانات كتاب
    public function update(Request $request, $id)
    {
        $book = Book::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'title' => 'string|max:255',
            'author' => 'string|max:255',
            'price' => 'numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }
            $book->image = $request->file('image')->store('books', 'public');
        }

        $book->update($request->only(['title', 'author', 'price', 'description', 'image']));

        return response()->json([
            'message' => 'Book updated successfully',
            'data' => $book
        ]);
    }

    // ✅ حذف كتاب
    public function destroy(Request $request, $id)
    {
        $book = Book::where('user_id', $request->user()->id)->findOrFail($id);

        if ($book->image) {
            Storage::disk('public')->delete($book->image);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully'
        ]);
    }
}

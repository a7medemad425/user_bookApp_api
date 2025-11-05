<?php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // إضافة كتاب للمفضلة
    public function store($bookId)
    {
        $book = Book::findOrFail($bookId);
        $user = auth()->user();

        // تأكد إنه مش مضاف بالفعل
        if ($user->favorites()->where('book_id', $bookId)->exists()) {
            return response()->json(['message' => 'Already in favorites'], 200);
        }

        $user->favorites()->attach($bookId);
        return response()->json(['message' => 'Book added to favorites']);
    }

    // عرض المفضلة
    public function index()
    {
        $favorites = auth()->user()->favorites()->with('user')->get();
        return response()->json($favorites);
    }

    // حذف من المفضلة
    public function destroy($bookId)
    {
        $user = auth()->user();
        $user->favorites()->detach($bookId);

        return response()->json(['message' => 'Book removed from favorites']);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $categoryId = $request->input('category');

        // Secara default, hasil buku kosong
        $books = collect();

        // Jalankan pencarian hanya jika keyword atau kategori diisi
        if ($request->filled('keyword') || $request->filled('category')) {

            $query = Book::with('category');

            // Pencarian berdasarkan judul atau penulis
            if ($request->filled('keyword')) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'LIKE', "%{$keyword}%")
                      ->orWhere('author', 'LIKE', "%{$keyword}%");
                });
            }

            // Filter berdasarkan kategori
            if ($request->filled('category')) {
                $query->where('category_id', $categoryId);
            }

            $books = $query->latest()->get();
        }

        $categories = Category::orderBy('name')->get();

        return view('books.index', compact(
            'books',
            'categories',
            'keyword',
            'categoryId'
        ));
    }
}

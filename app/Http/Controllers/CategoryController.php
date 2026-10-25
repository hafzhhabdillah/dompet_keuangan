<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori (bawaan & kategori khusus user).
     */
    public function index(): View
    {
        $userId = Auth::id();

        // Kategori bawaan sistem
        $defaultCategories = Category::whereNull('user_id')
            ->orderBy('tipe')
            ->orderBy('nama')
            ->get();

        // Kategori khusus milik user yang sedang login
        $userCategories = Category::where('user_id', $userId)
            ->withCount('transactions')
            ->orderBy('tipe')
            ->orderBy('nama')
            ->get();

        return view('categories.index', [
            'defaultCategories' => $defaultCategories,
            'userCategories' => $userCategories,
        ]);
    }

    /**
     * Tampilkan form pembuatan kategori kustom.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Simpan kategori kustom baru untuk user.
     */
    public function store(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                // Cegah duplikasi nama pada kategori user sendiri
                Rule::unique('categories')->where(function ($query) use ($userId, $request) {
                    return $query->where('tipe', $request->tipe)
                        ->where('user_id', $userId);
                }),
            ],
            'tipe' => ['required', 'in:pemasukan,pengeluaran'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Anda sudah memiliki kategori dengan nama dan tipe ini.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'tipe.in' => 'Tipe kategori harus berupa pemasukan atau pengeluaran.',
        ]);

        Category::create([
            'nama' => $validated['nama'],
            'tipe' => $validated['tipe'],
            'user_id' => $userId,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori kustom baru berhasil dibuat.');
    }

    /**
     * Tampilkan form edit kategori.
     */
    public function edit(Category $category): View
    {
        // Cegah akses atau edit kategori bawaan atau milik user lain
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda hanya dapat mengubah kategori buatan Anda sendiri.');
        }

        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Perbarui data kategori kustom milik user.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $userId = Auth::id();

        if ($category->user_id !== $userId) {
            abort(403, 'Akses Ditolak: Anda hanya dapat mengubah kategori buatan Anda sendiri.');
        }

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(function ($query) use ($userId, $request) {
                    return $query->where('tipe', $request->tipe)
                        ->where('user_id', $userId);
                })->ignore($category->id),
            ],
            'tipe' => ['required', 'in:pemasukan,pengeluaran'],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Anda sudah memiliki kategori dengan nama dan tipe ini.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'tipe.in' => 'Tipe kategori harus berupa pemasukan atau pengeluaran.',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori kustom milik user.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Akses Ditolak: Anda hanya dapat menghapus kategori buatan Anda sendiri.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

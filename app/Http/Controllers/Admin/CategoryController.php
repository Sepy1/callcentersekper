<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create($data);
        return redirect()->back()->with('category_success', 'Kategori ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $cat = Category::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191|unique:categories,name,' . $cat->id,
            'description' => 'nullable|string',
        ]);

        $cat->update($data);
        return redirect()->back()->with('category_success', 'Kategori diperbarui');
    }

    public function destroy($id)
    {
        $cat = Category::findOrFail($id);
        $cat->delete();
        return redirect()->back()->with('category_success', 'Kategori dihapus');
    }
}

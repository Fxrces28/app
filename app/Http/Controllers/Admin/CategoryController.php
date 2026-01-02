<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('meditations')->orderBy('name')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }
    
    public function create()
    {
        return view('admin.categories.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        Category::create($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Категория успешно создана');
    }
    
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        $category->update($validated);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Категория успешно обновлена');
    }
    
    public function destroy(Request $request, Category $category)
    {
        try {
            $meditationCount = $category->meditations()->count();
            
            if ($meditationCount > 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Нельзя удалить категорию. В ней ' . $meditationCount . ' медитаций.'
                    ], 400);
                }
                
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Нельзя удалить категорию, в которой есть медитации');
            }
            
            $category->delete();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Категория успешно удалена'
                ]);
            }
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно удалена');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при удалении: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.categories.index')
                ->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }
}
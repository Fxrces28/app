<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('meditations')->orderBy('name')->paginate(15);
        return view('admin.tags.index', compact('tags'));
    }
    
    public function create()
    {
        return view('admin.tags.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:tags,name',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        Tag::create($validated);
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Тег успешно создан');
    }
    
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }
    
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:tags,name,' . $tag->id,
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        $tag->update($validated);
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Тег успешно обновлен');
    }
    
    public function destroy(Request $request, Tag $tag)
    {
        try {
            $meditationCount = $tag->meditations()->count();
            
            if ($meditationCount > 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Тег используется в ' . $meditationCount . ' медитациях. Сначала удалите связи.'
                    ], 400);
                }
                
                return redirect()->route('admin.tags.index')
                    ->with('error', 'Тег используется в ' . $meditationCount . ' медитациях. Сначала удалите связи.');
            }
            
            $tag->delete();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Тег успешно удален'
                ]);
            }
            
            return redirect()->route('admin.tags.index')
                ->with('success', 'Тег успешно удален');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при удалении: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.tags.index')
                ->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meditation;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MeditationController extends Controller
{
    public function index()
    {
        $meditations = Meditation::with('category')->paginate(10);
        return view('admin.meditations.index', compact('meditations'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.meditations.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'audio_file' => 'required|file|mimes:mp3,wav,m4a|max:10240', // 10MB
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'duration' => 'required|integer|min:60',
            'category_id' => 'required|exists:categories,id',
            'is_premium' => 'boolean'
        ]);

        $audioPath = $request->file('audio_file')->store('audio', 'public');
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $meditation = Meditation::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'description' => $request->description,
            'audio_path' => $audioPath,
            'image_path' => $imagePath,
            'duration' => $request->duration,
            'category_id' => $request->category_id,
            'is_premium' => $request->has('is_premium'),
        ]);

        if ($request->has('tags')) {
            $meditation->tags()->sync($request->tags);
        }

        return redirect()->route('admin.meditations.index')
            ->with('success', 'Медитация успешно создана!');
    }

    public function show(string $id)
    {
    }

    public function edit(Meditation $meditation)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $selectedTags = $meditation->tags->pluck('id')->toArray();
        
        return view('admin.meditations.edit', compact('meditation', 'categories', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Meditation $meditation)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav,m4a|max:10240',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'duration' => 'required|integer|min:60',
            'category_id' => 'required|exists:categories,id',
            'is_premium' => 'boolean'
        ]);

        if ($request->hasFile('audio_file')) {
            if ($meditation->audio_path && file_exists(storage_path('app/public/' . $meditation->audio_path))) {
                unlink(storage_path('app/public/' . $meditation->audio_path));
            }
            $meditation->audio_path = $request->file('audio_file')->store('audio', 'public');
        }

        if ($request->hasFile('image')) {
            if ($meditation->image_path && file_exists(storage_path('app/public/' . $meditation->image_path))) {
                unlink(storage_path('app/public/' . $meditation->image_path));
            }
            $meditation->image_path = $request->file('image')->store('images', 'public');
        }

        $meditation->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . $meditation->id,
            'description' => $request->description,
            'duration' => $request->duration,
            'category_id' => $request->category_id,
            'is_premium' => $request->has('is_premium'),
        ]);

        $meditation->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.meditations.index')
            ->with('success', 'Медитация успешно обновлена!');
    }


    public function destroy(Request $request, Meditation $meditation)
    {
        try {
            if ($meditation->audio_path && Storage::exists('public/' . $meditation->audio_path)) {
                Storage::delete('public/' . $meditation->audio_path);
            }
            
            if ($meditation->image_path && Storage::exists('public/' . $meditation->image_path)) {
                Storage::delete('public/' . $meditation->image_path);
            }
            
            $meditation->tags()->detach();
            
            $meditation->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Медитация успешно удалена'
                ]);
            }

            return redirect()->route('admin.meditations.index')
                ->with('success', 'Медитация успешно удалена!');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при удалении: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.meditations.index')
                ->with('error', 'Ошибка при удаления: ' . $e->getMessage());
        }
    }
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withTimestamps();
    }
}

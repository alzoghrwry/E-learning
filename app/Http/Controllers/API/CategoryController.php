<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    // ----------------------
    // Public endpoint (no auth)
    // ----------------------
    public function publicCategories()
    {
        $categories = Category::withCount('courses')->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'status_code' => 200,
            'data' => CategoryResource::collection($categories)
        ]);
    }

    // ----------------------
    // Admin CRUD
    // ----------------------

    // عرض جميع الفئات (Admin)
    public function index()
    {
        $categories = Category::withCount('courses')->get();

        return response()->json([
            'message' => 'Categories retrieved successfully',
            'status_code' => 200,
            'data' => CategoryResource::collection($categories)
        ]);
    }

    // إنشاء فئة جديدة
    public function store(CategoryRequest $request)
    {
        $thumbnailUrl = null;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('categories', 'public');
            $thumbnailUrl = asset('storage/' . $path);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'thumbnail' => $thumbnailUrl,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'status_code' => 201,
            'data' => new CategoryResource($category)
        ], 201);
    }

    // تحديث فئة
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status_code' => 404
            ], 404);
        }

        if ($request->hasFile('thumbnail')) {
            if ($category->thumbnail) {
                $oldPath = str_replace(asset('storage/'), '', $category->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('thumbnail')->store('categories', 'public');
            $category->thumbnail = asset('storage/' . $path);
        }

        $category->name = $request->name ?? $category->name;
        $category->description = $request->description ?? $category->description;
        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'status_code' => 200,
            'data' => new CategoryResource($category)
        ]);
    }

    // عرض فئة واحدة
    public function show($id)
    {
        $category = Category::with('courses')->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status_code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Category retrieved successfully',
            'status_code' => 200,
            'data' => new CategoryResource($category)
        ]);
    }

    // حذف فئة
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status_code' => 404
            ], 404);
        }

        if ($category->thumbnail) {
            $oldPath = str_replace(asset('storage/'), '', $category->thumbnail);
            Storage::disk('public')->delete($oldPath);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
            'status_code' => 200
        ]);
    }
}

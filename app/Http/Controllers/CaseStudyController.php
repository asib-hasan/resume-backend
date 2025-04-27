<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CaseStudyController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => CaseStudy::orderBy('sort_order')->get()
        ], 200);
    }

    public function get_single($id)
    {
        $blog = CaseStudy::find($id);

        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'CaseStudy not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $blog
        ], 200);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'category' => 'required|string|max:150',
            'date' => 'required|date',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'title.required' => 'Title required.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'category.required' => 'Category required.',
            'category.max' => 'Category cannot exceed 150 characters.',
            'date.required' => 'Date required.',
            'date.date' => 'Invalid date format.',
            'description.required' => 'Description required.',
            'image.image' => 'Invalid image format.',
            'image.mimes' => 'Image must be jpg, jpeg, png, or webp.',
            'image.max' => 'Image size should not exceed 2MB.',
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {
                $path = $request->hasFile('image') ? $request->file('image')->store('blogs', 'public') : null;

                CaseStudy::create([
                    'title' => $validated['title'],
                    'category' => $validated['category'],
                    'date' => $validated['date'],
                    'description' => $validated['description'],
                    'image' => $path,
                    'status' => 'active',
                ]);
            });

            return response()->json(['message' => 'CaseStudy created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create CaseStudy.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $blog = CaseStudy::find($id);

        if (!$blog) {
            return response()->json(['message' => 'CaseStudy information not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'category' => 'required|max:150',
            'date' => 'required|date',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'title.required' => 'Title required.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'category.required' => 'Category required.',
            'category.max' => 'Category cannot exceed 150 characters.',
            'date.required' => 'Date required.',
            'date.date' => 'Invalid date format.',
            'description.required' => 'Description required.',
            'image.image' => 'Invalid image format.',
            'image.mimes' => 'Image must be jpg, jpeg, png, or webp.',
            'image.max' => 'Image size should not exceed 2MB.',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $blog) {
                $data = [
                    'title' => $validated['title'],
                    'category' => $validated['category'],
                    'date' => $validated['date'],
                    'description' => $validated['description'],
                ];

                if ($request->hasFile('image')) {
                    if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                        Storage::disk('public')->delete($blog->image);
                    }

                    $data['image'] = $request->file('image')->store('blogs', 'public');
                }

                $blog->update($data);
            });

            return response()->json([
                'message' => 'CaseStudy updated successfully!',
                'data' => $blog
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update CaseStudy.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $blog = CaseStudy::find($id);

        if (!$blog) {
            return response()->json(['message' => 'CaseStudy not found'], 404);
        }

        try {
            DB::transaction(function () use ($blog) {
                if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                    Storage::disk('public')->delete($blog->image);
                }

                $blog->delete();
            });

            return response()->json(['message' => 'CaseStudy deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete CaseStudy.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update_status(Request $request, $id)
    {
        $blog = CaseStudy::find($id);

        if (!$blog) {
            return response()->json(['message' => 'CaseStudy information not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required'
        ], [
            'status.required' => 'Status required.',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $blog) {
                $data = [
                    'status' => $validated['status'],
                ];

                $blog->update($data);
            });

            return response()->json([
                'message' => 'CaseStudy updated successfully!',
                'data' => $blog
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update CaseStudy.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

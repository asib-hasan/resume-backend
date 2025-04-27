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
        $case_study = CaseStudy::find($id);

        if (!$case_study) {
            return response()->json([
                'status' => 'error',
                'message' => 'CaseStudy not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $case_study
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
                $path = $request->hasFile('image') ? $request->file('image')->store('case_study', 'public') : null;

                CaseStudy::create([
                    'title' => $validated['title'],
                    'category' => $validated['category'],
                    'date' => $validated['date'],
                    'description' => $validated['description'],
                    'image' => $path,
                    'status' => 'active',
                ]);
            });

            return response()->json(['message' => 'Case study created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Case study.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $case_study = CaseStudy::find($id);

        if (!$case_study) {
            return response()->json(['message' => 'Case study information not found'], 404);
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
            DB::transaction(function () use ($request, $validated, $case_study) {
                $data = [
                    'title' => $validated['title'],
                    'category' => $validated['category'],
                    'date' => $validated['date'],
                    'description' => $validated['description'],
                ];

                if ($request->hasFile('image')) {
                    if ($case_study->image && Storage::disk('public')->exists($case_study->image)) {
                        Storage::disk('public')->delete($case_study->image);
                    }

                    $data['image'] = $request->file('image')->store('case_study', 'public');
                }

                $case_study->update($data);
            });

            return response()->json([
                'message' => 'Case study updated successfully!',
                'data' => $case_study
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update Case study.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $case_study = CaseStudy::find($id);

        if (!$case_study) {
            return response()->json(['message' => 'Case study not found'], 404);
        }

        try {
            DB::transaction(function () use ($case_study) {
                if ($case_study->image && Storage::disk('public')->exists($case_study->image)) {
                    Storage::disk('public')->delete($case_study->image);
                }

                $case_study->delete();
            });

            return response()->json(['message' => 'Case study deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete Case study.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update_status(Request $request, $id)
    {
        $case_study = CaseStudy::find($id);

        if (!$case_study) {
            return response()->json(['message' => 'Case study information not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required'
        ], [
            'status.required' => 'Status required.',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $case_study) {
                $data = [
                    'status' => $validated['status'],
                ];

                $case_study->update($data);
            });

            return response()->json([
                'message' => 'Case study updated successfully!',
                'data' => $case_study
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update Case study.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

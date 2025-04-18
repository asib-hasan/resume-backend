<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    // Get all languages
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Language::orderBy('sort_order')->get()
        ], 200);
    }

    // Store new language
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|string|max:100',
        ]);

        $total = Language::count();

        try {
            DB::transaction(function () use ($validated, $total) {
                Language::create([
                    'name' => $validated['name'],
                    'level' => $validated['level'],
                    'sort_order' => $total + 1,
                ]);
            });

            return response()->json(['message' => 'Language created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create language.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update language
    public function update(Request $request, $id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json(['message' => 'Language not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'level' => 'required|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($language, $validated) {
                $language->update([
                    'name' => $validated['name'],
                    'level' => $validated['level'],
                ]);
            });

            return response()->json([
                'message' => 'Language updated successfully!',
                'data' => $language
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update language.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete language
    public function destroy($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json(['message' => 'Language not found'], 404);
        }

        try {
            DB::transaction(function () use ($language) {
                $language->delete();
            });

            return response()->json(['message' => 'Language deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete language.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update sort order
    public function sort_order(Request $request)
    {
        DB::beginTransaction();

        try {
            foreach ($request->languagess as $langData) {
                $language = Language::find($langData['id']);
                if ($language) {
                    $language->sort_order = $langData['sort_order'];
                    $language->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Sort order updated successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update sort order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

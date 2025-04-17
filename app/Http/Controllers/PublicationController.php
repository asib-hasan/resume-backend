<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicationController extends Controller
{
    // Get all publications
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Publication::orderBy('sort_order')->get()
        ], 200);
    }

    // Store new publication
    public function store(Request $request)
    {
        $validated = $request->validate([
            'details' => 'required|string|max:1000',
            'link' => 'nullable|url|max:500',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $total = Publication::count();

        try {
            DB::transaction(function () use ($validated, $total) {
                Publication::create([
                    'details' => $validated['details'],
                    'link' => $validated['link'] ?? null,
                    'year' => $validated['year'],
                    'sort_order' => $total + 1,
                ]);
            });

            return response()->json(['message' => 'Publication created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create publication.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update publication
    public function update(Request $request, $id)
    {
        $publication = Publication::find($id);

        if (!$publication) {
            return response()->json(['message' => 'Publication not found'], 404);
        }

        $validated = $request->validate([
            'details' => 'required|string|max:1000',
            'link' => 'nullable|url|max:500',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        try {
            DB::transaction(function () use ($publication, $validated) {
                $publication->update([
                    'details' => $validated['details'],
                    'link' => $validated['link'] ?? null,
                    'year' => $validated['year'],
                ]);
            });

            return response()->json([
                'message' => 'Publication updated successfully!',
                'data' => $publication
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update publication.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete publication
    public function destroy($id)
    {
        $publication = Publication::find($id);

        if (!$publication) {
            return response()->json(['message' => 'Publication not found'], 404);
        }

        try {
            DB::transaction(function () use ($publication) {
                $publication->delete();
            });

            return response()->json(['message' => 'Publication deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete publication.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update sort order
    public function sort_order(Request $request)
    {
        DB::beginTransaction();

        try {
            foreach ($request->publications as $pubData) {
                $publication = Publication::find($pubData['id']);
                if ($publication) {
                    $publication->sort_order = $pubData['sort_order'];
                    $publication->save();
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

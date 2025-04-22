<?php

namespace App\Http\Controllers;

use App\Models\Award;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AwardsController extends Controller
{
    // Get all awards
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Award::orderBy('sort_order')->get()
        ], 200);
    }

    // Store new award
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $total = Award::count();

        try {
            DB::transaction(function () use ($validated, $total) {
                Award::create([
                    'title' => $validated['title'],
                    'status' => $validated['status'],
                    'sort_order' => $total + 1,
                ]);
            });

            return response()->json(['message' => 'Award created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create award.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update award
    public function update(Request $request, $id)
    {
        $award = Award::find($id);

        if (!$award) {
            return response()->json(['message' => 'Award not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            DB::transaction(function () use ($award, $validated) {
                $award->update([
                    'title' => $validated['title'],
                    'status' => $validated['status'],
                ]);
            });

            return response()->json([
                'message' => 'Award updated successfully!',
                'data' => $award
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update award.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete award
    public function destroy($id)
    {
        $award = Award::find($id);

        if (!$award) {
            return response()->json(['message' => 'Award not found'], 404);
        }

        try {
            DB::transaction(function () use ($award) {
                $award->delete();
            });

            return response()->json(['message' => 'Award deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete award.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update sort order
    public function sort_order(Request $request)
    {
        DB::beginTransaction();

        try {
            foreach ($request->awards as $awardData) {
                $award = Award::find($awardData['id']);
                if ($award) {
                    $award->sort_order = $awardData['sort_order'];
                    $award->save();
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

<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterestController extends Controller
{
    // Get all interests
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Interest::orderBy('sort_order')->get()
        ], 200);
    }

    // Store new interest
    public function store(Request $request)
    {
        $validated = $request->validate([
            'area' => 'required|max:255',
        ]);

        $total_interests = Interest::count();

        try {
            DB::transaction(function () use ($validated, $total_interests) {
                Interest::create([
                    'area' => $validated['area'],
                    'sort_order' => $total_interests + 1,
                ]);
            });

            return response()->json([
                'message' => 'Interest created successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create interest.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update interest
    public function update(Request $request, $id)
    {
        $interest = Interest::find($id);

        if (!$interest) {
            return response()->json(['message' => 'Interest not found'], 404);
        }

        $validated = $request->validate([
            'area' => 'required|max:255',
        ]);

        try {
            DB::transaction(function () use ($interest, $validated) {
                $interest->update([
                    'area' => $validated['area'],
                ]);
            });

            return response()->json([
                'message' => 'Interest updated successfully!',
                'data' => $interest
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update interest.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete interest
    public function destroy($id)
    {
        $interest = Interest::where('id',$id)->first();

        if (!$interest) {
            return response()->json(['message' => 'Interest not found'], 404);
        }

        try {
            DB::transaction(function () use ($interest) {
                $interest->delete();
            });

            return response()->json(['message' => 'Interest deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete interest.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Update sort order
    public function sort_order(Request $request)
    {
        DB::beginTransaction();

        try {
            foreach ($request->interests as $interestData) {
                $interest = Interest::find($interestData['id']);
                if ($interest) {
                    $interest->sort_order = $interestData['sort_order'];
                    $interest->save();
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

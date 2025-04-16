<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkillsController extends Controller
{
    // Get all skills
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Skill::orderBy('sort_order')->get()
        ], 200);
    }

    // Store new skill
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'level' => 'required|max:100',
        ]);

        $total_skills = Skill::count();

        try {
            DB::transaction(function () use ($validated, $total_skills) {
                Skill::create([
                    'name' => $validated['name'],
                    'level' => $validated['level'],
                    'sort_order' => $total_skills + 1,
                ]);
            });

            return response()->json([
                'message' => 'Skill created successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create skill.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update skill
    public function update(Request $request, $id)
    {
        $skill = Skill::find($id);

        if (!$skill) {
            return response()->json(['message' => 'Skill not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'level' => 'required|max:100',
        ]);

        try {
            DB::transaction(function () use ($skill, $validated) {
                $skill->update([
                    'name' => $validated['name'],
                    'level' => $validated['level'],
                ]);
            });

            return response()->json([
                'message' => 'Skill updated successfully!',
                'data' => $skill
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update skill.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete skill
    public function destroy($id)
    {
        $skill = Skill::find($id);

        if (!$skill) {
            return response()->json(['message' => 'Skill not found'], 404);
        }

        $skill->delete();

        return response()->json(['message' => 'Skill deleted successfully'], 200);
    }

    // Update sort order
    public function sort_order(Request $request)
    {
        DB::beginTransaction();

        try {
            foreach ($request->skills as $skillData) {
                $skill = Skill::find($skillData['id']);
                if ($skill) {
                    $skill->sort_order = $skillData['sort_order'];
                    $skill->save();
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

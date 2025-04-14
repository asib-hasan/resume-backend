<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => (Experience::all())
        ], 200);
    }



    # create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string',
            'company_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'responsibilities' => 'nullable|string',
        ]);
        $total_experience = Experience::count();
        try {

            DB::transaction(function () use ($validated, $total_experience) {
                Experience::insert([
                    'job_title' => $validated['job_title'],
                    'company_name' => $validated['company_name'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'] ?? null,
                    'responsibilities' => $validated['responsibilities'],
                    'sort_order' => $total_experience + 1,
                ]);
            });
            return response()->json([
                'message' => 'Experience created successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create experience.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    # update
    public function update(Request $request, $id)
    {
        $experience_info = Experience::where('id', $id)->first();

        if (!$experience_info) {
            return response()->json(['message' => 'Experience information not Found'], 404);
        }

        $validated = $request->validate([
            'job_title' => 'required|string',
            'company_name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'responsibilities' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($experience_info, $validated) {
                $experience_info->update([
                    'job_title' => $validated['job_title'],
                    'company_name' => $validated['company_name'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'] ?? null,
                    'responsibilities' => $validated['responsibilities'],
                ]);
            });

            return response()->json([
                'message' => 'Experience updated successfully!',
                'data' => $experience_info
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update experience.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    # delete
    public function destroy($id)
    {
        $experience = Experience::where('id',$id)->first();
        if (!$experience) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $experience->delete();
        return response()->json(['message' => 'Experience deleted successfully'],200);
    }

    public function sort_order(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->experiences as $experienceData) {
                $experience_info = Experience::where('id',$experienceData['id'])->first();
                if ($experience_info) {
                    $experience_info->sort_order = $experienceData['sort_order'];
                    $experience_info->save();
                }
            }
            DB::commit();
            return response()->json(['message' => 'Sort order updated successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update sort order', 'error' => $e->getMessage()], 500);
        }
    }
}

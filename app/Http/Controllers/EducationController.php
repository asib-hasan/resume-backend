<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EducationController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => (Education::orderBy('sort_order')->get())
        ], 200);
    }



    # create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institute' => 'required|max:255',
            'city' => 'required|max:255',
            'degree' => 'required|max:255',
            'field_of_study' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'result' => 'required|max:10',
        ]);
        $total_education = Education::count();
        try {

            DB::transaction(function () use ($validated, $total_education) {
                Education::insert([
                    'institute' => $validated['institute'],
                    'city' => $validated['city'],
                    'degree' => $validated['degree'],
                    'field_of_study' => $validated['field_of_study'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'result' => $validated['result'],
                    'sort_order' => $total_education + 1,
                ]);
            });
            return response()->json([
                'message' => 'Education information created successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create education.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    # update
    public function update(Request $request, $id)
    {
        $education_info = Education::where('id', $id)->first();

        if (!$education_info) {
            return response()->json(['message' => 'Education information not Found'], 404);
        }

        $validated = $request->validate([
            'institute' => 'required|max:255',
            'city' => 'required|max:255',
            'degree' => 'required|max:255',
            'field_of_study' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'result' => 'required|max:10',
        ]);

        try {
            DB::transaction(function () use ($education_info, $validated) {
                $education_info->update([
                    'institute' => $validated['institute'],
                    'city' => $validated['city'],
                    'degree' => $validated['degree'],
                    'field_of_study' => $validated['field_of_study'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'result' => $validated['result'],
                ]);
            });

            return response()->json([
                'message' => 'Education information updated successfully!',
                'data' => $education_info
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update education information.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    # delete
    public function destroy($id)
    {
        $education_info = Education::where('id',$id)->first();
        if (!$education_info) {
            return response()->json(['message' => 'Education information not found'], 404);
        }

        $education_info->delete();
        return response()->json(['message' => 'Education information deleted successfully'],200);
    }

    public function sort_order(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->educations as $educationsData) {
                $education_info = Education::where('id',$educationsData['id'])->first();
                if ($education_info) {
                    $education_info->sort_order = $educationsData['sort_order'];
                    $education_info->save();
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

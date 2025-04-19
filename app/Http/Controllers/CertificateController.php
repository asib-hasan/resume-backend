<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    // Get all certificates
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Certificate::orderBy('sort_order')->get()
        ], 200);
    }

    // Store a new certificate
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $total = Certificate::count();

        try {
            DB::transaction(function () use ($request, $validated, $total) {
                $path = $request->file('image')->store('certificates', 'public');

                Certificate::create([
                    'title' => $validated['title'],
                    'image' => $path,
                    'sort_order' => $total + 1,
                ]);
            });

            return response()->json(['message' => 'Certificate created successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create certificate.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update certificate
    public function update(Request $request, $id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $certificate, $validated) {
                $data = ['title' => $validated['title']];

                if ($request->hasFile('image')) {
                    // Delete old image if exists
                    $oldPath = str_replace(asset('storage') . '/', '', $certificate->image);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }

                    // Store new image
                    $path = $request->file('image')->store('certificates', 'public');
                    $data['image'] = asset('storage/' . $path);
                }

                $certificate->update($data);
            });

            return response()->json([
                'message' => 'Certificate updated successfully!',
                'data' => $certificate
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update certificate.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete certificate
    public function destroy($id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        try {
            DB::transaction(function () use ($certificate) {
                // Delete image file
                $imagePath = str_replace(asset('storage') . '/', '', $certificate->image);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                $certificate->delete();
            });

            return response()->json(['message' => 'Certificate deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete certificate.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update sort order
    public function sort_order(Request $request)
    {
        DB::beginTransaction();

        try {
            foreach ($request->certifications as $certData) {
                $certificate = Certificate::find($certData['id']);
                if ($certificate) {
                    $certificate->sort_order = $certData['sort_order'];
                    $certificate->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Sort order updated successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update sort order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

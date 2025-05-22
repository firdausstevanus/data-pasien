<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helpers\EncryptionHelper;

class DokterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check authorization
        if (Gate::denies('viewAny', Dokter::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $dokters = Dokter::all();
        
        // Log access to this endpoint
        activity()
            ->causedBy(auth()->user())
            ->log('view all dokter via API');
            
        return response()->json($dokters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check authorization
        if (Gate::denies('create', Dokter::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
            'email' => 'required|email|unique:dokters,email',
        ]);
        
        $dokter = Dokter::create($validated);
        
        // Log creation of dokter
        activity()
            ->causedBy(auth()->user())
            ->performedOn($dokter)
            ->log('created dokter via API');
            
        return response()->json([
            'message' => 'Dokter berhasil dibuat',
            'data' => $dokter
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dokter = Dokter::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('view', $dokter)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Log view of this dokter
        activity()
            ->causedBy(auth()->user())
            ->performedOn($dokter)
            ->log('viewed dokter via API');
            
        return response()->json($dokter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dokter = Dokter::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('update', $dokter)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'spesialisasi' => 'sometimes|string|max:255',
            'no_telepon' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:dokters,email,' . $id,
        ]);
        
        $dokter->update($validated);
        
        // Log update of dokter
        activity()
            ->causedBy(auth()->user())
            ->performedOn($dokter)
            ->log('updated dokter via API');
            
        return response()->json([
            'message' => 'Dokter berhasil diperbarui',
            'data' => $dokter
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dokter = Dokter::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('delete', $dokter)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Log deletion of dokter
        activity()
            ->causedBy(auth()->user())
            ->performedOn($dokter)
            ->log('deleted dokter via API');
            
        $dokter->delete();
        
        return response()->json([
            'message' => 'Dokter berhasil dihapus'
        ]);
    }
}

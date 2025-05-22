<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helpers\EncryptionHelper;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check authorization
        if (Gate::denies('viewAny', Pasien::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $pasiens = Pasien::all();
        
        // Log access to this endpoint
        activity()
            ->causedBy(auth()->user())
            ->log('view all pasien via API');
            
        return response()->json($pasiens);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check authorization
        if (Gate::denies('create', Pasien::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:20',
            'email' => 'required|email|unique:pasiens,email',
        ]);
        
        $pasien = Pasien::create($validated);
        
        // Log creation of pasien
        activity()
            ->causedBy(auth()->user())
            ->performedOn($pasien)
            ->log('created pasien via API');
            
        return response()->json([
            'message' => 'Pasien berhasil dibuat',
            'data' => $pasien
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pasien = Pasien::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('view', $pasien)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Log view of this pasien
        activity()
            ->causedBy(auth()->user())
            ->performedOn($pasien)
            ->log('viewed pasien via API');
            
        return response()->json($pasien);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pasien = Pasien::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('update', $pasien)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'tanggal_lahir' => 'sometimes|date',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'alamat' => 'sometimes|string',
            'no_telepon' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:pasiens,email,' . $id,
        ]);
        
        $pasien->update($validated);
        
        // Log update of pasien
        activity()
            ->causedBy(auth()->user())
            ->performedOn($pasien)
            ->log('updated pasien via API');
            
        return response()->json([
            'message' => 'Pasien berhasil diperbarui',
            'data' => $pasien
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pasien = Pasien::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('delete', $pasien)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Log deletion of pasien
        activity()
            ->causedBy(auth()->user())
            ->performedOn($pasien)
            ->log('deleted pasien via API');
            
        $pasien->delete();
        
        return response()->json([
            'message' => 'Pasien berhasil dihapus'
        ]);
    }
}

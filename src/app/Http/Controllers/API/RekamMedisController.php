<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helpers\EncryptionHelper;

class RekamMedisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check authorization
        if (Gate::denies('viewAny', RekamMedis::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Jika user adalah dokter, hanya tampilkan rekam medis yang ditangani dokter tersebut
        if (auth()->user()->hasRole('dokter')) {
            $dokterId = auth()->user()->dokter->id ?? null;
            if ($dokterId) {
                $rekamMedis = RekamMedis::where('dokter_id', $dokterId)->with(['pasien', 'dokter'])->get();
            } else {
                return response()->json(['message' => 'Tidak ada data dokter terkait pengguna ini'], 404);
            }
        } else {
            // Untuk admin atau super_admin, tampilkan semua rekam medis
            $rekamMedis = RekamMedis::with(['pasien', 'dokter'])->get();
        }
        
        // Log access to this endpoint
        activity()
            ->causedBy(auth()->user())
            ->log('view all rekam medis via API');
            
        return response()->json($rekamMedis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check authorization
        if (Gate::denies('create', RekamMedis::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'dokter_id' => 'required|exists:dokters,id',
            'tanggal' => 'required|date',
            'diagnosa' => 'required|string',
            'pengobatan' => 'required|string',
        ]);
        
        // Jika user adalah dokter, pastikan dokter_id sesuai dengan dokter yang login
        if (auth()->user()->hasRole('dokter')) {
            $dokterId = auth()->user()->dokter->id ?? null;
            if ($dokterId && $dokterId != $validated['dokter_id']) {
                return response()->json(['message' => 'Anda hanya dapat membuat rekam medis untuk diri sendiri sebagai dokter'], 403);
            }
        }
        
        $rekamMedis = RekamMedis::create($validated);
        
        // Log creation of rekam medis
        activity()
            ->causedBy(auth()->user())
            ->performedOn($rekamMedis)
            ->log('created rekam medis via API');
            
        return response()->json([
            'message' => 'Rekam Medis berhasil dibuat',
            'data' => $rekamMedis
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rekamMedis = RekamMedis::with(['pasien', 'dokter'])->findOrFail($id);
        
        // Check authorization
        if (Gate::denies('view', $rekamMedis)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Log view of this rekam medis
        activity()
            ->causedBy(auth()->user())
            ->performedOn($rekamMedis)
            ->log('viewed rekam medis via API');
            
        return response()->json($rekamMedis);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('update', $rekamMedis)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'pasien_id' => 'sometimes|exists:pasiens,id',
            'dokter_id' => 'sometimes|exists:dokters,id',
            'tanggal' => 'sometimes|date',
            'diagnosa' => 'sometimes|string',
            'pengobatan' => 'sometimes|string',
        ]);
        
        // Jika user adalah dokter, pastikan dokter_id sesuai dengan dokter yang login
        if (auth()->user()->hasRole('dokter')) {
            $dokterId = auth()->user()->dokter->id ?? null;
            if ($dokterId) {
                if (isset($validated['dokter_id']) && $validated['dokter_id'] != $dokterId) {
                    return response()->json(['message' => 'Anda tidak dapat mengubah dokter untuk rekam medis ini'], 403);
                }
                
                if ($rekamMedis->dokter_id != $dokterId) {
                    return response()->json(['message' => 'Anda hanya dapat mengedit rekam medis yang Anda tangani'], 403);
                }
            }
        }
        
        $rekamMedis->update($validated);
        
        // Log update of rekam medis
        activity()
            ->causedBy(auth()->user())
            ->performedOn($rekamMedis)
            ->log('updated rekam medis via API');
            
        return response()->json([
            'message' => 'Rekam Medis berhasil diperbarui',
            'data' => $rekamMedis
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        
        // Check authorization
        if (Gate::denies('delete', $rekamMedis)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Log deletion of rekam medis
        activity()
            ->causedBy(auth()->user())
            ->performedOn($rekamMedis)
            ->log('deleted rekam medis via API');
            
        $rekamMedis->delete();
        
        return response()->json([
            'message' => 'Rekam Medis berhasil dihapus'
        ]);
    }
}

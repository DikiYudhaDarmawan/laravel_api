<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fan;
use Illuminate\Http\Request;
use Validator;

class FanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fans = Fan::with('klub')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Fans',
            'data' => $fans,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = validator::make($request->all(), [
            'nama_fan' => 'required',
            'klub' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->getMessage(),
            ], 422);
        }
        try {
            $fan = new Fan();
            $fan->nama_fan = $request->nama_fan;
            $fan->save();
            //lampiran banyak klub
            $fan->klub()->attach($request->klub);

            return response()->json([
                'success' => true,
                'message' => 'data berhasil dibuat',
                'data' => $fan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = validator::make($request->all(), [
            'nama_fan' => 'required',
            'klub' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->getMessage(),
            ], 422);
        }
        try {
            $fan =  Fan::findOrFail($id);
            $fan->nama_fan = $request->nama_fan;
            $fan->save();
            //lampiran banyak klub
            $fan->klub()->sync($request->klub);

            return response()->json([
                'success' => true,
                'message' => 'data berhasil dibuat',
                'data' => $fan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $fan =  Fan::findOrFail($id);
            $fan->klub()->detach();
            $fan->delete();
    //hapus banyak klub
    return response()->json([
        'success' => true,
        'message' => 'data berhasil dihapus',
        'data' => $fan,
    ], 201);
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => 'terjadi kesalahan',
        'errors' => $e->getMessage(),
    ], 500);
}

    }
}

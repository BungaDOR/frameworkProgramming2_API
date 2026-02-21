<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukApi;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Resources\ProdukResource;
use Illuminate\Http\Request;

class ProdukApiController extends Controller
{
    public function index()
    {
        $produk = ProdukApi::paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'List Produk',
            'data' => ProdukResource::collection($produk)
        ]);
    }

    public function store(StoreProdukRequest $request)
    {
        $produk = ProdukApi::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dibuat',
            'data' => new ProdukResource($produk)
        ], 201);
    }

    public function show($id)
    {
        $produk = ProdukApi::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ProdukResource($produk)
        ]);
    }

    public function update(StoreProdukRequest $request, $id)
    {
        $produk = ProdukApi::findOrFail($id);
        $produk->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => new ProdukResource($produk)
        ]);
    }

    public function destroy($id)
    {
        $produk = ProdukApi::findOrFail($id);
        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}

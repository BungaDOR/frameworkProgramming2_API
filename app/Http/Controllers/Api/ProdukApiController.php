<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukApi;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Resources\ProdukResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\ProdukImage;

class ProdukApiController extends Controller
{
    public function index(Request $request)
    {
        $query = ProdukApi::query();

        // SEARCH
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('namaBarang', 'like', "%$search%")
                  ->orWhere('kodeBarang', 'like', "%$search%");
        }

        // FILTER KATEGORI
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // SORTING
        if ($request->has('sort')) {
            if ($request->sort === 'harga_asc') {
                $query->orderBy('harga', 'asc');
            } elseif ($request->sort === 'harga_desc') {
                $query->orderBy('harga', 'desc');
            }
        } else {
            $query->latest();
        }

        $produk = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'List Produk',
            'data' => ProdukResource::collection($produk),
            'pagination' => [
                'current_page' => $produk->currentPage(),
                'last_page' => $produk->lastPage(),
                'per_page' => $produk->perPage(),
                'total' => $produk->total(),
            ]
        ]);
    }

    public function store(StoreProdukRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time().'_'.$file->getClientOriginalName();
            $destinationPath = storage_path('app/public/produk/'.$filename);

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $image->scale(width: 800);
            $image->save($destinationPath);

            $data['gambar'] = 'produk/'.$filename;
        }

        $produk = ProdukApi::create($data);

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
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }

            $file = $request->file('gambar');
            $filename = time().'_'.$file->getClientOriginalName();
            $destinationPath = storage_path('app/public/produk/'.$filename);

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            if ($image->width() > 800) {
                $image->scale(width: 800);
            }

            $image->save($destinationPath, quality: 80);
            $data['gambar'] = 'produk/'.$filename;
        }

        $produk->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => new ProdukResource($produk)
        ]);
    }

    public function destroy($id)
    {
        $produk = ProdukApi::findOrFail($id);

        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}
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

        //SEARCH
        if ($request->has('search')) {
            $search = $request->search;

            $query->where('namaBarang', 'like', "%$search%")
                ->orWhere('kodeBarang', 'like', "%$search%");
        }

        // FILTER KATEGORI
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // SORTING HARGA
        if ($request->has('sort')) {
            $sort = $request->sort;
            
            if ($sort === 'harga_asc') {
                $query->orderBy('harga', 'asc');
            } 

            if ($sort === 'harga_desc') {
                $query->orderBy('harga', 'desc');
            }
        } else {
            $query->latest();
        }

        $produk = $query->paginate(10); // Bisa diubah sesuai kebutuhan, misalnya paginate(20) untuk 20 item per halaman

        return response()->json([
            'success' => true,
            'message' => 'List Produk',
            'data' => ProdukResource::collection($produk),
            'pagination' => [
                'current_page' => $produk->currentPage(),
                'last_page' => $produk->lastPage(),
                'per_page' => $produk->perPage(),
                'total' => $produk->total(),

                'from' => $produk->firstItem(),
                'to' => $produk->lastItem(),

                'first_page_url' => $produk->url(1),
                'last_page_url' => $produk->url($produk->lastPage()),
                'next_page_url' => $produk->nextPageUrl(),
                'prev_page_url' => $produk->previousPageUrl(),
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

            // engine resize image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // $image->resize(800, null);
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

        if (empty($data)) {
            return response()->json([
                'message' => 'Tidak ada data yang diupdate'
            ]);
        }
        // Cek apakah ada gambar baru
        if ($request->hasfile('gambar')) {

            // Hapus gambar lama jika ada
            if ($produk->gambar) {
                Storage::disk('public')->delete($produk->gambar);
            }

            $file = $request->file('gambar');

            $filename = time().'_'.$file->getClientOriginalName();

            $destinationPath = storage_path('app/public/produk/'.$filename);

            // resize engine
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());

            // pakai scale
            if ($image->width() > 800) {
                $image->scale(width: 800);
            }

            // save + compress
            $image->save($destinationPath, quality: 80);

            // simpan ke DB
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

    public function uploadImages(Request $request, $id)
    {
        $produk = ProdukApi::findOrFail($id);

        $request->validate([
            'gambar'=>'required|array',
            'gambar.*'=>'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $manager = new ImageManager(new Driver());

        $images = [];

        foreach ($request->file('gambar') as $file) {

            $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();

            $destinationPath = storage_path('app/public/produk/'.$filename);

            $image = $manager->read($file->getRealPath());

            if ($image->width() > 800) {
                $image->scale(width: 800);
            }

            $image->save($destinationPath, quality: 80);

            $path = 'produk/'.$filename;

            //  simpan ke tabel relasi
            $img = ProdukImage::create([
                'produk_id' => $produk->id,
                'path' => $path
            ]);

            $images[] = $img;
    }
    
        return response()->json([
            'success' => true,
            'message' => 'Multiple images berhasil diupload',
            'data' => $images
        ]);
    }

    public function updateImages(Request $request, $id)
    {
        $produk = ProdukApi::findOrFail($id);

        $request->validate([
            'gambar' => 'required|array',
            'gambar.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $manager = new ImageManager(new Driver());
        $files = $request->file('gambar');

        $images = $produk->images;

        foreach ($images as $index => $image) {

            if (isset($files[$index])) {

                if ($image->path) {
                    Storage::disk('public')->delete($image->path);
                }

                $file = $files[$index];

                $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();

                $destinationPath = storage_path('app/public/produk/'.$filename);

                $img = $manager->read($file->getRealPath());

                if ($img->width() > 800) {
                    $img->scale(width: 800);
                }

                $img->save($destinationPath, quality: 80);

                $image->update([
                    'path' => 'produk/'.$filename
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Images berhasil diupdate',
            'data' => $produk->images
        ]);
    }

    public function deleteImages($id, $imageId)
    {
        $produk = ProdukApi::findOrFail($id);
        $image = ProdukImage::findOrFail($imageId);

        if ($image->produk_id != $produk->id) {
            return response()->json([
                'success' => false,
                'message' => 'Gambar tidak ditemukan untuk produk ini'
            ], 404);
        }

        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gambar berhasil dihapus'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\UsulanBuku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UsulanBukuResource;

class UsulanBukuController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all posts
        $usulans = UsulanBuku::latest()->paginate(5);

        //return collection of usulans as a resource
        return new UsulanBukuResource(true, 'List Data Usulan Buku', $usulans);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'image'         => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'judul'         => 'required|string',
            'isbn'          => 'required|string|unique:usulan_buku,isbn',
            'penulis'       => 'required|string',
            'penerbit'      => 'required|string',
            'tahun_terbit'  => 'required|digits:4|integer|min:1900|max:' . (date('Y')),
            'kategori'      => 'required|string',
            'pengusul_email'=> 'required|email',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image
        $image = $request->file('image');
        $image->storeAs('public/usulans', $image->hashName());

        // Create usulan buku
        $usulan = UsulanBuku::create([
            'judul'         => $request->judul,
            'isbn'          => $request->isbn,
            'penulis'       => $request->penulis,
            'penerbit'      => $request->penerbit,
            'tahun_terbit'  => $request->tahun_terbit,
            'kategori'      => $request->kategori,
            'pengusul_email'=> $request->pengusul_email,
            'image'         => $image->hashName(),
        ]);

        // Return response
        return new UsulanBukuResource(true, 'Data Usulan Buku Berhasil Ditambahkan!', $usulan);
    }


    
    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        //find post by ID
        $usulan = UsulanBuku::find($id);

        //return single UsulanBuku as a resource
        return new UsulanBukuResource(true, 'Detail Data Usulan Buku!', $usulan);
    }



    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'judul'         => 'required|string',
            'isbn'          => 'required|string|unique:usulan_buku,isbn,' . $id,
            'penulis'       => 'required|string',
            'penerbit'      => 'required|string',
            'tahun_terbit'  => 'required|digits:4|integer|min:1900|max:' . (date('Y')),
            'kategori'      => 'required|string',
            'pengusul_email'=> 'required|email',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find usulan buku by ID
        $usulan = UsulanBuku::find($id);

        if (!$usulan) {
            return response()->json(['error' => 'Data Usulan Buku tidak ditemukan'], 404);
        }

        // Check if image is not empty
        if ($request->hasFile('image')) {
            // Upload image
            $image = $request->file('image');
            $image->storeAs('public/usulans', $image->hashName());

            // Delete old image
            if ($usulan->image) {
                Storage::delete('public/usulans/' . $usulan->image);
            }

            // Update usulan with new image
            $usulan->update([
                'image'         => $image->hashName(),
                'judul'         => $request->judul,
                'isbn'          => $request->isbn,
                'penulis'       => $request->penulis,
                'penerbit'      => $request->penerbit,
                'tahun_terbit'  => $request->tahun_terbit,
                'kategori'      => $request->kategori,
                'pengusul_email'=> $request->pengusul_email,
            ]);
        } else {
            // Update usulan without image
            $usulan->update([
                'judul'         => $request->judul,
                'isbn'          => $request->isbn,
                'penulis'       => $request->penulis,
                'penerbit'      => $request->penerbit,
                'tahun_terbit'  => $request->tahun_terbit,
                'kategori'      => $request->kategori,
                'pengusul_email'=> $request->pengusul_email,
            ]);
        }

        // Return response
        return new UsulanBukuResource(true, 'Data Usulan Buku Berhasil Diubah!', $usulan);
    }



      /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {

        //find post by ID
        $usulan = UsulanBuku::find($id);

        //delete image
        Storage::delete('public/usulans/'.basename($usulan->image));

        //delete usulan
        $usulan->delete();

        //return response
        return new UsulanBukuResource(true, 'Data Usulan Buku Berhasil Dihapus!', null);
    }


}

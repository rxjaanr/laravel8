<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    protected $siswa;

    public function __construct(Siswa $siswa)
    {
        // Inject Model Ke Controller
        $this->siswa = $siswa;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Tampilkan semua siswa
        $siswas = $this->siswa->all();
        $response = [
            'message' => 'Data Semua Siswa',
            'data' => $siswas
        ];
        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Simpan data siswa ke database

        // validasi data yang masuk agar tidak ada yang kosong
        $rules = [
            'nama' => 'required',
            'alamat' => 'required',
            'nisn' => 'required|min:8',
            'foto' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ];

        $validator = Validator::make($request->all(), $rules);

        // Tampilkan pesan jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422 /* Status Code */);
        }

        $foto = $request->file('foto');

        $uploadedPath = Storage::put('folder-gambar', $foto);

        $result = $this->siswa->create([
            // Data dari request body
            'nama' => $request->post('nama'),
            'alamat' => $request->post('alamat'),
            'nisn' => $request->post('nisn'),
            'foto' => $uploadedPath,
        ]);

        $response = [
            'message' => 'Data Siswa Berhasil disimpan',
            'data' => $result
        ];

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Tampilkan Satu siswa berdasarkan id

        // Cari Siswa Berdasar Id
        $siswa = $this->siswa->findOrFail($id);

        // Jika tidak ditemukan maka kirim response gagal
        if (!$siswa) {
            return response()->json([
                'error' => 'Siswa Tidak Ditemukan'
            ], 404);
        }

        $response = [
            'message' => 'Data Siswa',
            'data' => $siswa
        ];

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Edit Data Siswa Berdasarkan Id
        // Cari Siswa Berdasar Id
        $siswa = $this->siswa->findOrFail($id);

        // Jika tidak ditemukan maka kirim response gagal
        if (!$siswa) {
            return response()->json([
                'error' => 'Siswa Tidak Ditemukan'
            ], 404);
        }

        $siswa->update($request->all());

        $response = [
            'message' => 'Data Siswa Berhasil Di Update',
            'data' => $siswa
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Hapus Data Siswa

        $siswa = $this->siswa->findOrFail($id);

        // Jika tidak ditemukan maka kirim response gagal
        if (!$siswa) {
            return response()->json([
                'error' => 'Siswa Tidak Ditemukan'
            ], 404);
        }
        Storage::delete($siswa->foto);
        $siswa->delete();
        return response()->json(['message' => 'Data Siswa Berhasil dihapus']);
    }
}

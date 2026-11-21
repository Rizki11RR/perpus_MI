<?php
/**
 * Tujuan: menyediakan endpoint export CSV master data yang diizinkan.
 * Caller: tombol Export pada halaman Kategori Buku dan Data Siswa.
 * Dependensi: MasterDataCsvService dan middleware route auth/active.
 * Public utama: books() dan students().
 * Side effect: membaca database dan mengirim file CSV melalui HTTP.
 */

namespace App\Http\Controllers;

use App\Services\MasterDataCsvService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterDataCsvController extends Controller
{
    public function books(MasterDataCsvService $service): StreamedResponse
    {
        return $service->exportBooks();
    }

    public function students(MasterDataCsvService $service): StreamedResponse
    {
        return $service->exportStudents();
    }
}

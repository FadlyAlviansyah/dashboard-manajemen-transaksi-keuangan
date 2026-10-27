<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    private $filePath = 'data/transaksi.csv';

    // Membaca data dari file CSV (Aman dari baris kosong)
    private function getCsvData()
    {
        if (!Storage::exists($this->filePath)) {
            // Header CSV lengkap dengan kolom 4 minggu
            $header = "id,bulan,pendapatan,pengeluaran,m1_pen,m1_peng,m2_pen,m2_peng,m3_pen,m3_peng,m4_pen,m4_peng\n";
            $sampleData = "1,Januari,16500000,9300000,4000000,2000000,4500000,2300000,4000000,2500000,4000000,2500000\n";
            $sampleData .= "2,Februari,17800000,11000000,4500000,2500000,4500000,2500000,4300000,3000000,4500000,3000000\n";
            Storage::put($this->filePath, $header . $sampleData);
        }

        $content = Storage::get($this->filePath);
        $lines = explode("\n", trim($content));
        $data = [];

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (!empty($line)) {
                $row = str_getcsv($line);
                
                // Hanya ambil baris yang minimal punya nama Bulan (mengabaikan baris kosong sampah)
                $bulan = trim($row[1] ?? '');
                if ($bulan !== '') {
                    $data[] = (object) [
                        'id'                  => trim($row[0] ?? ($i)),
                        'bulan'               => $bulan,
                        'pendapatan'          => (float) ($row[2] ?? 0),
                        'pengeluaran'         => (float) ($row[3] ?? 0),
                        'minggu_1_pendapatan' => (float) ($row[4] ?? 0),
                        'minggu_1_pengeluaran'=> (float) ($row[5] ?? 0),
                        'minggu_2_pendapatan' => (float) ($row[6] ?? 0),
                        'minggu_2_pengeluaran'=> (float) ($row[7] ?? 0),
                        'minggu_3_pendapatan' => (float) ($row[8] ?? 0),
                        'minggu_3_pengeluaran'=> (float) ($row[9] ?? 0),
                        'minggu_4_pendapatan' => (float) ($row[10] ?? 0),
                        'minggu_4_pengeluaran'=> (float) ($row[11] ?? 0),
                    ];
                }
            }
        }

        return $data;
    }

    // Tampilkan Halaman Utama Transaksi / Dashboard Integration
    public function index()
    {
        $transaksi = collect($this->getCsvData());

        $totalPendapatan  = $transaksi->sum('pendapatan');
        $totalPengeluaran = $transaksi->sum('pengeluaran');
        $totalLaba        = $totalPendapatan - $totalPengeluaran;
        $totalMargin      = $totalPendapatan > 0 ? ($totalLaba / $totalPendapatan) * 100 : 0;

        return view('transaksi.index', compact(
            'transaksi', 
            'totalPendapatan', 
            'totalPengeluaran', 
            'totalLaba', 
            'totalMargin'
        ));
    }

    // Tampilkan Form Tambah Transaksi Baru
    public function create()
    {
        return view('transaksi.create');
    }

    // Simpan Data Transaksi Baru
    public function store(Request $request)
    {
        $request->validate([
            'bulan'                 => 'required',
            'minggu_1_pendapatan'   => 'required|numeric',
            'minggu_1_pengeluaran'  => 'required|numeric',
            'minggu_2_pendapatan'   => 'required|numeric',
            'minggu_2_pengeluaran'  => 'required|numeric',
            'minggu_3_pendapatan'   => 'required|numeric',
            'minggu_3_pengeluaran'  => 'required|numeric',
            'minggu_4_pendapatan'   => 'required|numeric',
            'minggu_4_pengeluaran'  => 'required|numeric',
        ]);

        $totalPendapatan = $request->minggu_1_pendapatan + $request->minggu_2_pendapatan + $request->minggu_3_pendapatan + $request->minggu_4_pendapatan;
        $totalPengeluaran = $request->minggu_1_pengeluaran + $request->minggu_2_pengeluaran + $request->minggu_3_pengeluaran + $request->minggu_4_pengeluaran;

        $data = $this->getCsvData();
        $newId = count($data) > 0 ? max(array_map(fn($item) => (int)$item->id, $data)) + 1 : 1;

        $newRow = "{$newId},{$request->bulan},{$totalPendapatan},{$totalPengeluaran},{$request->minggu_1_pendapatan},{$request->minggu_1_pengeluaran},{$request->minggu_2_pendapatan},{$request->minggu_2_pengeluaran},{$request->minggu_3_pendapatan},{$request->minggu_3_pengeluaran},{$request->minggu_4_pendapatan},{$request->minggu_4_pengeluaran}\n";
        Storage::append($this->filePath, trim($newRow));

        return redirect()->route('dashboard.table')->with('success', 'Data transaksi baru berhasil ditambahkan!');
    }

    // Form Edit Rincian Mingguan
    public function edit($id)
    {
        $data = $this->getCsvData();
        $transaksiItem = collect($data)->firstWhere('id', (string)$id);

        if (!$transaksiItem) {
            return redirect()->back()->with('error', 'Data transaksi tidak ditemukan.');
        }

        $transaksi = (array) $transaksiItem;
        return view('transaksi.edit', compact('transaksi'));
    }

    // Update Rincian Mingguan
    public function update(Request $request, $id)
    {
        $request->validate([
            'bulan'                 => 'required',
            'minggu_1_pendapatan'   => 'required|numeric',
            'minggu_1_pengeluaran'  => 'required|numeric',
            'minggu_2_pendapatan'   => 'required|numeric',
            'minggu_2_pengeluaran'  => 'required|numeric',
            'minggu_3_pendapatan'   => 'required|numeric',
            'minggu_3_pengeluaran'  => 'required|numeric',
            'minggu_4_pendapatan'   => 'required|numeric',
            'minggu_4_pengeluaran'  => 'required|numeric',
        ]);

        $totalPendapatan = $request->minggu_1_pendapatan + $request->minggu_2_pendapatan + $request->minggu_3_pendapatan + $request->minggu_4_pendapatan;
        $totalPengeluaran = $request->minggu_1_pengeluaran + $request->minggu_2_pengeluaran + $request->minggu_3_pengeluaran + $request->minggu_4_pengeluaran;

        $data = $this->getCsvData();
        $csvContent = "id,bulan,pendapatan,pengeluaran,m1_pen,m1_peng,m2_pen,m2_peng,m3_pen,m3_peng,m4_pen,m4_peng\n";

        foreach ($data as $row) {
            if ((string)$row->id === (string)$id) {
                $csvContent .= "{$id},{$request->bulan},{$totalPendapatan},{$totalPengeluaran},{$request->minggu_1_pendapatan},{$request->minggu_1_pengeluaran},{$request->minggu_2_pendapatan},{$request->minggu_2_pengeluaran},{$request->minggu_3_pendapatan},{$request->minggu_3_pengeluaran},{$request->minggu_4_pendapatan},{$request->minggu_4_pengeluaran}\n";
            } else {
                $csvContent .= "{$row->id},{$row->bulan},{$row->pendapatan},{$row->pengeluaran},{$row->minggu_1_pendapatan},{$row->minggu_1_pengeluaran},{$row->minggu_2_pendapatan},{$row->minggu_2_pengeluaran},{$row->minggu_3_pendapatan},{$row->minggu_3_pengeluaran},{$row->minggu_4_pendapatan},{$row->minggu_4_pengeluaran}\n";
            }
        }

        Storage::put($this->filePath, $csvContent);

        return redirect()->route('dashboard.table')->with('success', 'Rincian transaksi mingguan berhasil diperbarui!');
    }

    // Hapus Data Transaksi dari CSV (Kuat & Presisi)
    public function destroy($id)
    {
        $data = $this->getCsvData();

        // Tulis ulang file CSV dengan membuang baris yang ID-nya cocok
        $csvContent = "id,bulan,pendapatan,pengeluaran,m1_pen,m1_peng,m2_pen,m2_peng,m3_pen,m3_peng,m4_pen,m4_peng\n";
        
        foreach ($data as $row) {
            if ((string)$row->id !== (string)$id) {
                $csvContent .= "{$row->id},{$row->bulan},{$row->pendapatan},{$row->pengeluaran},{$row->minggu_1_pendapatan},{$row->minggu_1_pengeluaran},{$row->minggu_2_pendapatan},{$row->minggu_2_pengeluaran},{$row->minggu_3_pendapatan},{$row->minggu_3_pengeluaran},{$row->minggu_4_pendapatan},{$row->minggu_4_pengeluaran}\n";
            }
        }

        Storage::put($this->filePath, $csvContent);

        return redirect()->route('dashboard.table')->with('success', 'Data transaksi berhasil dihapus!');
    }
}

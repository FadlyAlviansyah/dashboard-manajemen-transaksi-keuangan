@extends('layouts.app')

@section('title', 'Tambah Transaksi Baru')

@section('content')
<div style="max-width: 700px; margin: 30px auto; background: white; padding: 28px; border-radius: 16px; border: 2.5px solid #E2E8F0; box-shadow: 0 4px 0 #E2E8F0;">
    <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 8px;">
        ➕ Tambah Data Transaksi Baru
    </h2>
    <p style="font-size: 0.88rem; color: #64748B; margin-bottom: 24px;">
        Isi nama bulan dan rincian transaksi 4 minggu di bawah ini.
    </p>

    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="font-weight: 700; font-size: 0.9rem; color: #334155;">Nama Bulan</label>
            <input type="text" name="bulan" placeholder="Contoh: Maret" required 
                   style="width: 100%; padding: 10px 14px; border: 2px solid #E2E8F0; border-radius: 10px; font-weight: 700; margin-top: 4px; outline: none;">
        </div>

        <div style="background: #F8FAFC; padding: 20px; border-radius: 14px; border: 2px solid #E2E8F0; margin-bottom: 24px;">
            <h4 style="font-weight: 800; color: #0F172A; margin-bottom: 16px;">Breakdown Mingguan (Rp)</h4>

            <div style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                <div style="width: 90px;"></div>
                <div style="flex: 1; font-weight: 800; font-size: 0.85rem; color: #00880D; background: #E8F8EE; padding: 6px 12px; border-radius: 8px; border: 1px solid #A7F3D0; text-align: center;">
                    💵 Pendapatan / Pemasukan
                </div>
                <div style="flex: 1; font-weight: 800; font-size: 0.85rem; color: #DC2626; background: #FEF2F2; padding: 6px 12px; border-radius: 8px; border: 1px solid #FCA5A5; text-align: center;">
                    💸 Pengeluaran
                </div>
            </div>

            @for($w = 1; $w <= 4; $w++)
            <div style="display: flex; gap: 12px; margin-bottom: 12px; align-items: center;">
                <span style="font-weight: 800; width: 90px; font-size: 0.85rem; color: #475569;">W{{ $w }} (Mng {{ $w }}):</span>
                <input type="number" name="minggu_{{ $w }}_pendapatan" class="input-pendapatan" placeholder="0" value="0" required oninput="hitungTotal()"
                       style="flex: 1; padding: 10px 14px; border: 2px solid #CBD5E1; border-radius: 10px; font-weight: 700; outline: none; background: white;">
                <input type="number" name="minggu_{{ $w }}_pengeluaran" class="input-pengeluaran" placeholder="0" value="0" required oninput="hitungTotal()"
                       style="flex: 1; padding: 10px 14px; border: 2px solid #CBD5E1; border-radius: 10px; font-weight: 700; outline: none; background: white;">
            </div>
            @endfor

            <div style="margin-top: 18px; padding-top: 14px; border-top: 2px dashed #CBD5E1; display: flex; gap: 12px; align-items: center;">
                <span style="font-weight: 800; width: 90px; font-size: 0.85rem; color: #0F172A;">Total (1 Bln):</span>
                <div style="flex: 1; padding: 10px 14px; background: #E8F8EE; border: 2px solid #A7F3D0; border-radius: 10px; font-weight: 800; color: #00880D; font-family: monospace;" id="display-total-pendapatan">Rp 0</div>
                <div style="flex: 1; padding: 10px 14px; background: #FEF2F2; border: 2px solid #FCA5A5; border-radius: 10px; font-weight: 800; color: #DC2626; font-family: monospace;" id="display-total-pengeluaran">Rp 0</div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('dashboard.table') }}" style="padding: 10px 20px; background: #F1F5F9; color: #475569; border-radius: 10px; text-decoration: none; font-weight: 800;">Batal</a>
            <button type="submit" style="padding: 10px 24px; background: #00AA13; color: white; border: none; border-bottom: 3.5px solid #00880D; border-radius: 10px; font-weight: 800; cursor: pointer;">Simpan Transaksi</button>
        </div>
    </form>
</div>

<script>
    function formatRupiah(angka) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka); }
    function hitungTotal() {
        let totalPen = 0, totalPeng = 0;
        document.querySelectorAll('.input-pendapatan').forEach(el => totalPen += parseFloat(el.value) || 0);
        document.querySelectorAll('.input-pengeluaran').forEach(el => totalPeng += parseFloat(el.value) || 0);
        document.getElementById('display-total-pendapatan').innerText = formatRupiah(totalPen);
        document.getElementById('display-total-pengeluaran').innerText = formatRupiah(totalPeng);
    }
    document.addEventListener("DOMContentLoaded", hitungTotal);
</script>
@endsection

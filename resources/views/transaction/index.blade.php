<div class="card" style="margin-top: 20px; background: white; padding: 24px; border-radius: 16px; border: 2.5px solid #E2E8F0; box-shadow: 0 4px 0 #E2E8F0;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 8px;">
            <span>💳</span>
            <span>Tabel Data Transaksi Keuangan</span>
        </h3>
        
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 0.78rem; font-weight: 700; color: #00880D; background: #E8F8EE; padding: 6px 12px; border-radius: 8px; border: 1px solid #A7F3D0;">
                CSV Storage Aktif
            </span>

            <a href="{{ route('transaction.create') }}" style="background: #00AA13; color: white; border: none; padding: 10px 20px; font-weight: 800; border-radius: 10px; cursor: pointer; border-bottom: 3.5px solid #00880D; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; font-size: 0.9rem;">
                <span>Tambah Transaksi</span>
            </a>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase;">Bulan</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase;">Pendapatan</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase;">Pengeluaran</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase;">Laba</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase;">Margin</th>
                    <th style="padding: 12px 16px; font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($transactions ?? []) as $index => $item)
                    @php
                        $pendapatan = is_array($item) ? ($item['pendapatan'] ?? 0) : ($item->pendapatan ?? 0);
                        $pengeluaran = is_array($item) ? ($item['pengeluaran'] ?? 0) : ($item->pengeluaran ?? 0);
                        $bulan = is_array($item) ? ($item['bulan'] ?? '-') : ($item->bulan ?? '-');
                        
                        $rawId = is_array($item) ? ($item['id'] ?? null) : ($item->id ?? null);
                        $itemId = (!empty($rawId)) ? $rawId : ($index + 1);

                        $laba = $pendapatan - $pengeluaran;
                        $margin = $pendapatan > 0 ? ($laba / $pendapatan) * 100 : 0;
                    @endphp
                    <tr style="border-bottom: 1.5px solid #F1F5F9;">
                        <td style="padding: 14px 16px; font-weight: 700; color: #0F172A;">{{ $bulan }}</td>
                        <td style="padding: 14px 16px; font-family: monospace; font-weight: 700;">Rp {{ number_format((float)$pendapatan, 0, ',', '.') }}</td>
                        <td style="padding: 14px 16px; font-family: monospace; font-weight: 700;">Rp {{ number_format((float)$pengeluaran, 0, ',', '.') }}</td>
                        <td style="padding: 14px 16px; font-family: monospace; font-weight: 800; color: #0284C7;">Rp {{ number_format((float)$laba, 0, ',', '.') }}</td>
                        <td style="padding: 14px 16px; font-weight: 700;">{{ number_format((float)$margin, 1) }}%</td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <a href="{{ route('transaction.edit', $itemId) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: white; border: 2px solid #E2E8F0; border-bottom: 3.5px solid #CBD5E1; border-radius: 10px; color: #0284C7; font-weight: 800; text-decoration: none; font-size: 0.85rem;" title="Edit Rincian 4 Minggu">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                    <span>Edit</span>
                                </a>

                                <button type="button" 
                                        style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 10px; background: white; border: 2px solid #FCA5A5; border-bottom: 3.5px solid #EF4444; border-radius: 10px; color: #EF4444; cursor: pointer;"
                                        onclick="openDeleteTransaksiModal('{{ $itemId }}', '{{ addslashes($bulan) }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #64748B; font-weight: 600;">
                            Belum ada data transaksi. Silakan klik + Tambah Transaksi di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(!empty($totalPendapatan))
            <tfoot>
                <tr style="font-weight: 800; background-color: #F8FAFC; border-top: 2px solid #E2E8F0;">
                    <td style="padding: 14px 16px;">Total Ringkasan</td>
                    <td style="padding: 14px 16px; font-family: monospace;">Rp {{ number_format((float)($totalPendapatan ?? 0), 0, ',', '.') }}</td>
                    <td style="padding: 14px 16px; font-family: monospace;">Rp {{ number_format((float)($totalPengeluaran ?? 0), 0, ',', '.') }}</td>
                    <td style="padding: 14px 16px; font-family: monospace; color: #00AA13;">Rp {{ number_format((float)($totalLaba ?? 0), 0, ',', '.') }}</td>
                    <td style="padding: 14px 16px;">{{ number_format((float)($totalMargin ?? 0), 1) }}%</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
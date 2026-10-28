<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TransactionCsvService
{
    protected string $filePath;

    protected array $headers = [
        'id',
        'bulan',
        'pendapatan',
        'pengeluaran',
        'm1_pen',
        'm1_peng',
        'm2_pen',
        'm2_peng',
        'm3_pen',
        'm3_peng',
        'm4_pen',
        'm4_peng',
    ];

    public function __construct()
    {
        $this->filePath = storage_path('app/data/transactions.csv');
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    protected function ensureFileExists(): void
    {
        $directory = dirname($this->filePath);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (! File::exists($this->filePath)) {
            $handle = fopen($this->filePath, 'w');
            if ($handle === false) {
                throw new \RuntimeException('Gagal membuat file transactions.csv.');
            }

            fputcsv($handle, $this->headers);
            fclose($handle);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAll(?string $search = null, ?string $bulan = null): array
    {
        $rows = $this->readRows();
        $search = $search !== null ? mb_strtolower(trim($search)) : null;
        $bulan = $bulan !== null ? trim($bulan) : null;

        return array_values(array_filter($rows, function (array $row) use ($search, $bulan): bool {
            if ($search !== null && $search !== '') {
                $haystack = mb_strtolower($row['id'].' '.$row['bulan']);
                if (mb_strpos($haystack, $search) === false) {
                    return false;
                }
            }

            return ! $bulan || strcasecmp($row['bulan'], $bulan) === 0;
        }));
    }

    public function paginate(
        int $perPage = 10,
        int $page = 1,
        ?string $search = null,
        ?string $bulan = null
    ): LengthAwarePaginator {
        $all = $this->getAll($search, $bulan);
        $page = max(1, $page);

        return new LengthAwarePaginator(
            array_slice($all, ($page - 1) * $perPage, $perPage),
            count($all),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function findById(string $id): ?array
    {
        foreach ($this->readRows() as $row) {
            if ((string) $row['id'] === trim($id)) {
                return $row;
            }
        }

        return null;
    }

    public function generateNextId(): string
    {
        $maxId = 0;
        foreach ($this->readRows() as $row) {
            if (ctype_digit((string) $row['id'])) {
                $maxId = max($maxId, (int) $row['id']);
            }
        }

        return (string) ($maxId + 1);
    }

    public function create(array $data): array
    {
        $this->ensureFileExists();

        $row = $this->makeRow($this->generateNextId(), $data);
        $handle = fopen($this->filePath, 'a');
        if ($handle === false) {
            throw new \RuntimeException('Gagal membuka file transactions.csv untuk penambahan data.');
        }

        if (! flock($handle, LOCK_EX)) {
            fclose($handle);
            throw new \RuntimeException('Gagal mengunci file transactions.csv untuk penambahan data.');
        }

        fputcsv($handle, $this->toCsvValues($row));
        flock($handle, LOCK_UN);
        fclose($handle);

        $this->syncToDatabase($row);

        return $row;
    }

    public function update(string $id, array $data): bool
    {
        $rows = $this->readRows();
        $updated = false;

        foreach ($rows as &$row) {
            if ((string) $row['id'] !== trim($id)) {
                continue;
            }

            $row = $this->makeRow($row['id'], array_merge($row, $data));
            $updated = true;
            break;
        }
        unset($row);

        if ($updated) {
            $this->writeRows($rows);
            $this->syncToDatabase($this->findById($id));
        }

        return $updated;
    }

    public function delete(string $id): bool
    {
        $rows = $this->readRows();
        $remainingRows = array_values(array_filter(
            $rows,
            fn (array $row): bool => (string) $row['id'] !== trim($id)
        ));

        if (count($remainingRows) === count($rows)) {
            return false;
        }

        $this->writeRows($remainingRows);
        DB::table('transactions')->where('id', (int) $id)->delete();

        return true;
    }

    public function getSummaryStats(): array
    {
        $rows = $this->readRows();
        $totalPendapatan = array_sum(array_column($rows, 'pendapatan'));
        $totalPengeluaran = array_sum(array_column($rows, 'pengeluaran'));
        $totalLaba = $totalPendapatan - $totalPengeluaran;

        return [
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalLaba' => $totalLaba,
            'totalMargin' => $totalPendapatan > 0 ? ($totalLaba / $totalPendapatan) * 100 : 0,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function readRows(): array
    {
        $this->ensureFileExists();

        $handle = fopen($this->filePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Gagal membaca file transactions.csv.');
        }

        $rawHeader = fgetcsv($handle);
        if (! $rawHeader) {
            fclose($handle);

            return [];
        }

        $rawHeader[0] = ltrim($rawHeader[0], "\xEF\xBB\xBF");
        $header = array_map('trim', $rawHeader);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($header)) {
                continue;
            }

            $row = array_combine($header, array_map('trim', $data));
            if ($row !== false) {
                $rows[] = $this->normalizeRow($row);
            }
        }

        fclose($handle);

        return $rows;
    }

    protected function normalizeRow(array $row): array
    {
        return [
            'id' => trim((string) ($row['id'] ?? '')),
            'bulan' => trim((string) ($row['bulan'] ?? '')),
            'pendapatan' => (float) ($row['pendapatan'] ?? 0),
            'pengeluaran' => (float) ($row['pengeluaran'] ?? 0),
            'minggu_1_pendapatan' => (float) ($row['minggu_1_pendapatan'] ?? $row['m1_pen'] ?? 0),
            'minggu_1_pengeluaran' => (float) ($row['minggu_1_pengeluaran'] ?? $row['m1_peng'] ?? 0),
            'minggu_2_pendapatan' => (float) ($row['minggu_2_pendapatan'] ?? $row['m2_pen'] ?? 0),
            'minggu_2_pengeluaran' => (float) ($row['minggu_2_pengeluaran'] ?? $row['m2_peng'] ?? 0),
            'minggu_3_pendapatan' => (float) ($row['minggu_3_pendapatan'] ?? $row['m3_pen'] ?? 0),
            'minggu_3_pengeluaran' => (float) ($row['minggu_3_pengeluaran'] ?? $row['m3_peng'] ?? 0),
            'minggu_4_pendapatan' => (float) ($row['minggu_4_pendapatan'] ?? $row['m4_pen'] ?? 0),
            'minggu_4_pengeluaran' => (float) ($row['minggu_4_pengeluaran'] ?? $row['m4_peng'] ?? 0),
        ];
    }

    protected function makeRow(string $id, array $data): array
    {
        $row = [
            'id' => $id,
            'bulan' => trim((string) ($data['bulan'] ?? '')),
        ];

        $totalPendapatan = 0.0;
        $totalPengeluaran = 0.0;

        for ($week = 1; $week <= 4; $week++) {
            $income = (float) ($data["minggu_{$week}_pendapatan"] ?? 0);
            $expense = (float) ($data["minggu_{$week}_pengeluaran"] ?? 0);

            $row["minggu_{$week}_pendapatan"] = $income;
            $row["minggu_{$week}_pengeluaran"] = $expense;
            $totalPendapatan += $income;
            $totalPengeluaran += $expense;
        }

        $row['pendapatan'] = $totalPendapatan;
        $row['pengeluaran'] = $totalPengeluaran;

        return $row;
    }

    /**
     * Persist the fields shared by the CSV and transactions table.
     *
     * @param  array<string, mixed>|null  $transaction
     */
    protected function syncToDatabase(?array $transaction): void
    {
        if ($transaction === null || ! ctype_digit((string) $transaction['id'])) {
            throw new \RuntimeException('ID transaksi CSV tidak valid untuk disimpan ke database.');
        }

        DB::table('transactions')->updateOrInsert(
            ['id' => (int) $transaction['id']],
            [
                'bulan' => $transaction['bulan'],
                'pendapatan' => $transaction['pendapatan'],
                'pengeluaran' => $transaction['pengeluaran'],
                'm1_pen' => $transaction['minggu_1_pendapatan'],
                'm1_peng' => $transaction['minggu_1_pengeluaran'],
                'm2_pen' => $transaction['minggu_2_pendapatan'],
                'm2_peng' => $transaction['minggu_2_pengeluaran'],
                'm3_pen' => $transaction['minggu_3_pendapatan'],
                'm3_peng' => $transaction['minggu_3_pengeluaran'],
                'm4_pen' => $transaction['minggu_4_pendapatan'],
                'm4_peng' => $transaction['minggu_4_pengeluaran'],
            ]
        );
    }

    protected function writeRows(array $rows): void
    {
        $tempPath = tempnam(dirname($this->filePath), 'transactions_');
        if ($tempPath === false) {
            throw new \RuntimeException('Gagal membuat file sementara untuk transactions.csv.');
        }

        $handle = fopen($tempPath, 'w');
        if ($handle === false) {
            @unlink($tempPath);
            throw new \RuntimeException('Gagal menulis file sementara untuk transactions.csv.');
        }

        fputcsv($handle, $this->headers);
        foreach ($rows as $row) {
            fputcsv($handle, $this->toCsvValues($row));
        }
        fclose($handle);

        if (! copy($tempPath, $this->filePath)) {
            @unlink($tempPath);
            throw new \RuntimeException('Gagal menyimpan perubahan ke transactions.csv.');
        }

        @unlink($tempPath);
    }

    protected function toCsvValues(array $row): array
    {
        return [
            $row['id'],
            $row['bulan'],
            $row['pendapatan'],
            $row['pengeluaran'],
            $row['minggu_1_pendapatan'],
            $row['minggu_1_pengeluaran'],
            $row['minggu_2_pendapatan'],
            $row['minggu_2_pengeluaran'],
            $row['minggu_3_pendapatan'],
            $row['minggu_3_pengeluaran'],
            $row['minggu_4_pendapatan'],
            $row['minggu_4_pengeluaran'],
        ];
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class CustomerCsvService
{
    protected string $filePath;

    protected array $headers = [
        'customer_id',
        'fisrt_name',
        'second_name',
        'gender',
        'date_of_birth',
        'city',
        'state',
        'occupation',
        'customer_segment',
        'annual_income',
        'join_date',
    ];

    public function __construct()
    {
        $this->filePath = storage_path('app/data/customers.csv');
    }

    /**
     * Mendapatkan path file CSV
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Memastikan file CSV dan foldernya ada
     */
    protected function ensureFileExists(): void
    {
        $dir = dirname($this->filePath);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if (!File::exists($this->filePath)) {
            $handle = fopen($this->filePath, 'w');
            fputcsv($handle, $this->headers);
            fclose($handle);
        }
    }

    /**
     * Membaca semua customer dari customers.csv
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(?string $search = null, ?string $segment = null, ?string $gender = null): array
    {
        $this->ensureFileExists();

        $rows = [];
        $handle = fopen($this->filePath, 'r');
        if ($handle === false) {
            return [];
        }

        // Ambil header
        $rawHeader = fgetcsv($handle);
        if (!$rawHeader) {
            fclose($handle);
            return [];
        }

        // Bersihkan BOM UTF-8 jika ada pada kolom pertama
        $rawHeader[0] = ltrim($rawHeader[0], "\xEF\xBB\xBF");
        $header = array_map('trim', $rawHeader);

        $search = $search ? mb_strtolower(trim($search)) : null;
        $segment = $segment ? trim($segment) : null;
        $gender = $gender ? trim($gender) : null;

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < count($header)) {
                continue;
            }

            $row = array_combine($header, array_map('trim', $data));
            
            // Tambahkan alias first_name & full_name untuk kemudahan blade
            $firstName = $row['fisrt_name'] ?? ($row['first_name'] ?? '');
            $secondName = $row['second_name'] ?? '';
            $row['first_name'] = $firstName;
            $row['fisrt_name'] = $firstName;
            $row['full_name'] = trim($firstName . ' ' . $secondName);

            // Filter pencarian
            if ($search !== null && $search !== '') {
                $haystack = mb_strtolower(
                    ($row['customer_id'] ?? '') . ' ' .
                    $row['full_name'] . ' ' .
                    ($row['city'] ?? '') . ' ' .
                    ($row['state'] ?? '') . ' ' .
                    ($row['occupation'] ?? '')
                );

                if (mb_strpos($haystack, $search) === false) {
                    continue;
                }
            }

            // Filter segmen
            if ($segment && strcasecmp($row['customer_segment'] ?? '', $segment) !== 0) {
                continue;
            }

            // Filter gender
            if ($gender && strcasecmp($row['gender'] ?? '', $gender) !== 0) {
                continue;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Mengambil data customer dengan paginasi (default 10 baris per tabel)
     */
    public function paginate(
        int $perPage = 10,
        int $page = 1,
        ?string $search = null,
        ?string $segment = null,
        ?string $gender = null
    ): LengthAwarePaginator {
        $all = $this->getAll($search, $segment, $gender);
        $total = count($all);

        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $itemsForCurrentPage = array_slice($all, $offset, $perPage);

        return new LengthAwarePaginator(
            $itemsForCurrentPage,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    /**
     * Mencari data customer berdasarkan customer_id
     */
    public function findById(string $id): ?array
    {
        $this->ensureFileExists();

        $handle = fopen($this->filePath, 'r');
        if ($handle === false) {
            return null;
        }

        $rawHeader = fgetcsv($handle);
        if (!$rawHeader) {
            fclose($handle);
            return null;
        }

        $rawHeader[0] = ltrim($rawHeader[0], "\xEF\xBB\xBF");
        $header = array_map('trim', $rawHeader);

        $id = trim($id);

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < count($header)) {
                continue;
            }

            $row = array_combine($header, array_map('trim', $data));
            if (strcasecmp($row['customer_id'] ?? '', $id) === 0) {
                fclose($handle);
                $firstName = $row['fisrt_name'] ?? ($row['first_name'] ?? '');
                $row['first_name'] = $firstName;
                $row['fisrt_name'] = $firstName;
                $row['full_name'] = trim($firstName . ' ' . ($row['second_name'] ?? ''));
                return $row;
            }
        }

        fclose($handle);
        return null;
    }

    /**
     * Menghasilkan Customer ID berikutnya secara otomatis (misal C05001)
     */
    public function generateNextId(): string
    {
        $this->ensureFileExists();

        $maxNum = 0;
        $handle = fopen($this->filePath, 'r');
        if ($handle !== false) {
            $rawHeader = fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== false) {
                if (!empty($data[0])) {
                    $id = trim($data[0]);
                    if (preg_match('/^C(\d+)$/i', $id, $matches)) {
                        $num = (int) $matches[1];
                        if ($num > $maxNum) {
                            $maxNum = $num;
                        }
                    }
                }
            }
            fclose($handle);
        }

        $nextNum = $maxNum + 1;
        return 'C' . str_pad((string)$nextNum, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Menambah customer baru ke file customers.csv
     */
    public function create(array $data): array
    {
        $this->ensureFileExists();

        $customerId = !empty($data['customer_id']) ? trim($data['customer_id']) : $this->generateNextId();

        // Validasi keunikan ID
        if ($this->findById($customerId) !== null) {
            $customerId = $this->generateNextId();
        }

        $firstName = trim($data['fisrt_name'] ?? ($data['first_name'] ?? ''));
        $secondName = trim($data['second_name'] ?? '');
        $gender = trim($data['gender'] ?? 'Male');
        $dob = $this->formatDateForCsv($data['date_of_birth'] ?? '');
        $city = trim($data['city'] ?? '');
        $state = trim($data['state'] ?? '');
        $occupation = trim($data['occupation'] ?? 'Salaried');
        $customerSegment = trim($data['customer_segment'] ?? 'Retail');
        $annualIncome = number_format((float) str_replace([',', ' '], '', $data['annual_income'] ?? 0), 2, '.', '');
        $joinDate = !empty($data['join_date']) ? $this->formatDateForCsv($data['join_date']) : Carbon::now()->format('d-m-Y');

        $newRow = [
            'customer_id' => $customerId,
            'fisrt_name' => $firstName,
            'second_name' => $secondName,
            'gender' => $gender,
            'date_of_birth' => $dob,
            'city' => $city,
            'state' => $state,
            'occupation' => $occupation,
            'customer_segment' => $customerSegment,
            'annual_income' => $annualIncome,
            'join_date' => $joinDate,
        ];

        // Tulis baris baru ke akhir file
        $handle = fopen($this->filePath, 'a');
        if ($handle === false) {
            throw new \RuntimeException("Gagal membuka file customers.csv untuk penambahan data.");
        }

        flock($handle, LOCK_EX);
        fputcsv($handle, array_values($newRow));
        flock($handle, LOCK_UN);
        fclose($handle);

        $newRow['first_name'] = $firstName;
        $newRow['full_name'] = trim($firstName . ' ' . $secondName);

        return $newRow;
    }

    /**
     * Memperbarui data customer di file customers.csv
     */
    public function update(string $customerId, array $data): bool
    {
        $this->ensureFileExists();

        $tempPath = $this->filePath . '.tmp.' . uniqid();
        $inHandle = fopen($this->filePath, 'r');
        $outHandle = fopen($tempPath, 'w');

        if ($inHandle === false || $outHandle === false) {
            if ($inHandle) fclose($inHandle);
            if ($outHandle) fclose($outHandle);
            return false;
        }

        flock($inHandle, LOCK_SH);
        flock($outHandle, LOCK_EX);

        $rawHeader = fgetcsv($inHandle);
        if (!$rawHeader) {
            flock($outHandle, LOCK_UN);
            flock($inHandle, LOCK_UN);
            fclose($inHandle);
            fclose($outHandle);
            @unlink($tempPath);
            return false;
        }

        // Tulis header asli kembali
        $rawHeader[0] = ltrim($rawHeader[0], "\xEF\xBB\xBF");
        $header = array_map('trim', $rawHeader);
        fputcsv($outHandle, $header);

        $updated = false;

        while (($row = fgetcsv($inHandle)) !== false) {
            if (count($row) < count($header)) {
                fputcsv($outHandle, $row);
                continue;
            }

            $rowData = array_combine($header, array_map('trim', $row));
            if (strcasecmp($rowData['customer_id'] ?? '', $customerId) === 0) {
                // Update baris ini
                $firstName = trim($data['fisrt_name'] ?? ($data['first_name'] ?? $rowData['fisrt_name']));
                $secondName = trim($data['second_name'] ?? $rowData['second_name']);
                $gender = trim($data['gender'] ?? $rowData['gender']);
                
                $dob = !empty($data['date_of_birth']) 
                    ? $this->formatDateForCsv($data['date_of_birth']) 
                    : $rowData['date_of_birth'];
                    
                $city = trim($data['city'] ?? $rowData['city']);
                $state = trim($data['state'] ?? $rowData['state']);
                $occupation = trim($data['occupation'] ?? $rowData['occupation']);
                $customerSegment = trim($data['customer_segment'] ?? $rowData['customer_segment']);
                
                $annualIncome = isset($data['annual_income']) 
                    ? number_format((float) str_replace([',', ' '], '', $data['annual_income']), 2, '.', '') 
                    : $rowData['annual_income'];
                    
                $joinDate = !empty($data['join_date']) 
                    ? $this->formatDateForCsv($data['join_date']) 
                    : $rowData['join_date'];

                $updatedRow = [
                    'customer_id' => $rowData['customer_id'],
                    'fisrt_name' => $firstName,
                    'second_name' => $secondName,
                    'gender' => $gender,
                    'date_of_birth' => $dob,
                    'city' => $city,
                    'state' => $state,
                    'occupation' => $occupation,
                    'customer_segment' => $customerSegment,
                    'annual_income' => $annualIncome,
                    'join_date' => $joinDate,
                ];

                fputcsv($outHandle, array_values($updatedRow));
                $updated = true;
            } else {
                fputcsv($outHandle, $row);
            }
        }

        flock($outHandle, LOCK_UN);
        flock($inHandle, LOCK_UN);
        fclose($inHandle);
        fclose($outHandle);

        if ($updated) {
            // Timpa file asli dengan file sementara secara aman
            return copy($tempPath, $this->filePath) && @unlink($tempPath);
        }

        @unlink($tempPath);
        return false;
    }

    /**
     * Menghapus baris customer dari customers.csv
     */
    public function delete(string $customerId): bool
    {
        $this->ensureFileExists();

        $tempPath = $this->filePath . '.tmp.' . uniqid();
        $inHandle = fopen($this->filePath, 'r');
        $outHandle = fopen($tempPath, 'w');

        if ($inHandle === false || $outHandle === false) {
            if ($inHandle) fclose($inHandle);
            if ($outHandle) fclose($outHandle);
            return false;
        }

        flock($inHandle, LOCK_SH);
        flock($outHandle, LOCK_EX);

        $rawHeader = fgetcsv($inHandle);
        if (!$rawHeader) {
            flock($outHandle, LOCK_UN);
            flock($inHandle, LOCK_UN);
            fclose($inHandle);
            fclose($outHandle);
            @unlink($tempPath);
            return false;
        }

        $rawHeader[0] = ltrim($rawHeader[0], "\xEF\xBB\xBF");
        $header = array_map('trim', $rawHeader);
        fputcsv($outHandle, $header);

        $deleted = false;

        while (($row = fgetcsv($inHandle)) !== false) {
            if (count($row) < count($header)) {
                fputcsv($outHandle, $row);
                continue;
            }

            $rowData = array_combine($header, array_map('trim', $row));
            if (strcasecmp($rowData['customer_id'] ?? '', $customerId) === 0) {
                $deleted = true;
                continue; // Lewati baris ini agar terhapus
            }

            fputcsv($outHandle, $row);
        }

        flock($outHandle, LOCK_UN);
        flock($inHandle, LOCK_UN);
        fclose($inHandle);
        fclose($outHandle);

        if ($deleted) {
            return copy($tempPath, $this->filePath) && @unlink($tempPath);
        }

        @unlink($tempPath);
        return false;
    }

    /**
     * Menghitung statistik ringkas untuk kartu dashboard customer
     */
    public function getSummaryStats(): array
    {
        $this->ensureFileExists();

        $totalCustomers = 0;
        $segments = [];
        $cities = [];
        $totalIncome = 0.0;

        $handle = fopen($this->filePath, 'r');
        if ($handle !== false) {
            $rawHeader = fgetcsv($handle);
            if ($rawHeader) {
                $rawHeader[0] = ltrim($rawHeader[0], "\xEF\xBB\xBF");
                $header = array_map('trim', $rawHeader);

                while (($data = fgetcsv($handle)) !== false) {
                    if (count($data) < count($header)) {
                        continue;
                    }
                    $row = array_combine($header, array_map('trim', $data));
                    $totalCustomers++;

                    $seg = $row['customer_segment'] ?? 'Other';
                    $segments[$seg] = ($segments[$seg] ?? 0) + 1;

                    $c = $row['city'] ?? '';
                    if ($c) {
                        $cities[$c] = true;
                    }

                    $inc = (float) ($row['annual_income'] ?? 0);
                    $totalIncome += $inc;
                }
            }
            fclose($handle);
        }

        return [
            'total_customers' => $totalCustomers,
            'total_cities' => count($cities),
            'segments' => $segments,
            'avg_income' => $totalCustomers > 0 ? $totalIncome / $totalCustomers : 0,
        ];
    }

    /**
     * Konversi format tanggal ke format CSV (d-m-Y)
     */
    protected function formatDateForCsv(string $date): string
    {
        $date = trim($date);
        if (empty($date)) {
            return Carbon::now()->format('d-m-Y');
        }

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y');
            }
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                return $date;
            }
            return Carbon::parse($date)->format('d-m-Y');
        } catch (\Throwable $e) {
            return $date;
        }
    }
}

<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportData extends Command
{
    protected $signature = 'data:import {--fresh : Kosongkan tabel sebelum import}';

    protected $description = 'Import dan bersihkan customers.csv dan transactions.csv';

    public function handle(): int
    {
        DB::disableQueryLog();

        if ($this->option('fresh')) {
            Schema::disableForeignKeyConstraints();
            DB::table('transactions')->truncate();
            DB::table('customers')->truncate();
            Schema::enableForeignKeyConstraints();
        }

        $this->importCustomers();
        $this->importTransactions();

        return self::SUCCESS;
    }

    private function importCustomers(): void
    {
        $now = now();
        $batch = [];
        $read = 0;
        $inserted = 0;

        foreach ($this->readCsv(storage_path('app/data/customers.csv')) as $r) {
            $read++;
            $batch[] = [
                'customer_id' => $r['customer_id'],
                'name' => $r['fisrt_name'].' '.$r['second_name'],
                'gender' => $r['gender'],
                'state' => $r['state'],
                'occupation' => $r['occupation'],
                'customer_segment' => $r['customer_segment'],
                'annual_income' => $r['annual_income'],
                'join_date' => $this->date($r['join_date']),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) === 1000) {
                DB::table('customers')->insert($batch);
                $inserted += count($batch);
                $batch = [];
            }
        }
        if ($batch) {
            DB::table('customers')->insert($batch);
            $inserted += count($batch);
        }

        $this->info("Customers: dibaca $read, masuk $inserted");
    }

    private function importTransactions(): void
    {
        $batch = [];
        $seen = [];
        $read = 0;
        $inserted = 0;
        $dupes = 0;

        foreach ($this->readCsv(storage_path('app/data/transactions.csv')) as $r) {
            $read++;

            if (isset($seen[$r['id']])) {
                $dupes++;

                continue;
            }
            $seen[$r['id']] = true;

            $batch[] = [
                'id' => $r['id'],
                'bulan' => $r['bulan'],
                'pendapatan' => $r['pendapatan'],
                'pengeluaran' => $r['pengeluaran'],
                'm1_pen' => $r['m1_pen'],
                'm1_peng' => $r['m1_peng'],
                'm2_pen' => $r['m2_pen'],
                'm2_peng' => $r['m2_peng'],
                'm3_pen' => $r['m3_pen'],
                'm3_peng' => $r['m3_peng'],
                'm4_pen' => $r['m4_pen'],
                'm4_peng' => $r['m4_peng'],
            ];

            if (count($batch) === 1000) {
                DB::table('transactions')->insert($batch);
                $inserted += count($batch);
                $batch = [];
            }
        }
        if ($batch) {
            DB::table('transactions')->insert($batch);
            $inserted += count($batch);
        }

        $this->info("Transactions: dibaca $read, masuk $inserted, duplikat dilewati $dupes");
    }

    private function readCsv(string $path): \Generator
    {
        $h = fopen($path, 'r');
        $header = fgetcsv($h);
        $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
        $header = array_map('trim', $header);

        while (($row = fgetcsv($h)) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }
            yield array_combine($header, array_map('trim', $row));
        }
        fclose($h);
    }

    private function date(string $value): string
    {
        return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
    }
}

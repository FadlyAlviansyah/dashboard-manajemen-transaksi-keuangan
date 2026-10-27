<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportData extends Command
{
    protected $signature = 'data:import {--fresh : Kosongkan tabel sebelum import}';
    protected $description = 'Import dan bersihkan customers.csv dan finance_transactions.csv';

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
        $batch = []; $read = 0; $inserted = 0;

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
        $now = now();
        $batch = []; $seen = [];
        $read = 0; $inserted = 0; $dupes = 0; $nonPositive = 0;

        foreach ($this->readCsv(storage_path('app/data/finance_transactions.csv')) as $r) {
            $read++;

            if (isset($seen[$r['transaction_id']])) {
                $dupes++;
                continue;
            }
            $seen[$r['transaction_id']] = true;

            if ((float) $r['amount'] <= 0) {
                $nonPositive++;
            }

            $batch[] = [
                'transaction_id' => $r['transaction_id'],
                'transaction_date' => $this->date($r['transaction_date']),
                'customer_id' => $r['customer_id'],
                'transaction_type' => $r['transaction_type'],
                'channel' => str_replace('@', 'o', $r['channel']),
                'merchant_category' => $r['merchant_category'],
                'amount' => $r['amount'],
                'transaction_status' => $r['transaction_status'],
                'is_fraud' => strtolower($r['is_fraud']) === 'yes' ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
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
        $this->warn("Transaksi dengan amount <= 0: $nonPositive (tunggu keputusan tim)");
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
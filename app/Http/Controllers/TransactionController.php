<?php

namespace App\Http\Controllers;

use App\Services\TransactionCsvService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    protected TransactionCsvService $csvService;

    public function __construct(TransactionCsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function index(): View
    {
        $transactions = collect($this->csvService->getAll());
        $stats = $this->csvService->getSummaryStats();

        return view('transaction.index', array_merge(
            compact('transactions'),
            $stats
        ));
    }

    public function create(): View
    {
        return view('transaction.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->transactionRules());
        $transaction = $this->csvService->create($validated);

        return redirect()->route('dashboard.table')
            ->with('success', "Transaksi bulan {$transaction['bulan']} berhasil ditambahkan.");
    }

    public function edit(string $id): View|RedirectResponse
    {
        $transaction = $this->csvService->findById($id);

        if ($transaction === null) {
            return redirect()->route('dashboard.table')
                ->with('error', "Data transaksi dengan ID '{$id}' tidak ditemukan.");
        }

        return view('transaction.edit', compact('transaction'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate($this->transactionRules());

        if ($this->csvService->update($id, $validated)) {
            return redirect()->route('dashboard.table')
                ->with('success', "Transaksi bulan {$validated['bulan']} berhasil diperbarui.");
        }

        return redirect()->route('dashboard.table')
            ->with('error', "Data transaksi dengan ID '{$id}' tidak ditemukan.");
    }

    public function destroy(string $id): RedirectResponse
    {
        if ($this->csvService->delete($id)) {
            return redirect()->route('dashboard.table')
                ->with('success', "Transaksi dengan ID '{$id}' berhasil dihapus.");
        }

        return redirect()->route('dashboard.table')
            ->with('error', "Data transaksi dengan ID '{$id}' tidak ditemukan.");
    }

    protected function transactionRules(): array
    {
        $rules = [
            'bulan' => 'required|string|max:30',
        ];

        for ($week = 1; $week <= 4; $week++) {
            $rules["minggu_{$week}_pendapatan"] = 'required|numeric|min:0';
            $rules["minggu_{$week}_pengeluaran"] = 'required|numeric|min:0';
        }

        return $rules;
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\CustomerCsvService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    protected CustomerCsvService $csvService;

    public function __construct(CustomerCsvService $csvService)
    {
        $this->csvService = $csvService;
    }

    public function dashboardAndTable(Request $request): View
    {
        $search = $request->input('search');
        $segment = $request->input('segment');
        $gender = $request->input('gender');
        $page = (int) $request->input('page', 1);
        $perPage = 10;

        $customers = $this->csvService->paginate($perPage, $page, $search, $segment, $gender);
        $stats = $this->csvService->getSummaryStats();
        $nextId = $this->csvService->generateNextId();

        $transactionController = app(TransactionController::class);
        $transactionResponse = $transactionController->index();
        $transactionData = method_exists($transactionResponse, 'getData')
            ? $transactionResponse->getData(true)
            : [];

        return view('dashboard-and-tabel', array_merge([
            'customers' => $customers,
            'stats' => $stats,
            'nextId' => $nextId,
            'search' => $search,
            'segment' => $segment,
            'gender' => $gender,
        ], $transactionData));
    }

    public function create(): View
    {
        $nextId = $this->csvService->generateNextId();
        $segments = ['Retail', 'SME', 'Premium', 'Corporate', 'Wealth'];
        $occupations = ['Salaried', 'Freelancer', 'Business Owner', 'Self Employed', 'Retired', 'Student'];

        return view('create-customer', compact('nextId', 'segments', 'occupations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:100',
            'second_name' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'occupation' => 'required|string|max:100',
            'customer_segment' => 'required|string|max:50',
            'annual_income' => 'required|numeric|min:0',
            'join_date' => 'nullable|string',
        ], [
            'first_name.required' => 'Nama depan wajib diisi',
            'second_name.required' => 'Nama belakang wajib diisi',
            'gender.required' => 'Pilih jenis kelamin',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'city.required' => 'Kota wajib diisi',
            'state.required' => 'Provinsi / State wajib diisi',
            'occupation.required' => 'Pekerjaan wajib diisi',
            'customer_segment.required' => 'Pilih segmen customer',
            'annual_income.required' => 'Pendapatan tahunan wajib diisi',
            'annual_income.numeric' => 'Pendapatan tahunan harus berupa angka',
        ]);

        $newCustomer = $this->csvService->create($validated);

        return redirect()->route('dashboard.table')
            ->with('success', "🎉 Customer {$newCustomer['full_name']} ({$newCustomer['customer_id']}) berhasil ditambahkan ke file customers.csv!");
    }

    public function edit(Request $request, ?string $id = null): View|RedirectResponse
    {
        $customerId = $id ?? $request->query('id');

        if (! $customerId) {
            return redirect()->route('dashboard.table')
                ->with('error', '⚠️ Silakan pilih customer yang ingin diupdate dari tabel.');
        }

        $customer = $this->csvService->findById($customerId);

        if (! $customer) {
            return redirect()->route('dashboard.table')
                ->with('error', "⚠️ Data customer dengan ID '{$customerId}' tidak ditemukan di file customers.csv.");
        }

        $segments = ['Retail', 'SME', 'Premium', 'Corporate', 'Wealth'];
        $occupations = ['Salaried', 'Freelancer', 'Business Owner', 'Self Employed', 'Retired', 'Student'];

        return view('update-customer', compact('customer', 'segments', 'occupations'));
    }

    public function update(Request $request, ?string $id = null): RedirectResponse
    {
        $customerId = $id ?? $request->input('customer_id');

        if (! $customerId) {
            return redirect()->route('dashboard.table')
                ->with('error', '⚠️ Customer ID tidak valid.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'second_name' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'occupation' => 'required|string|max:100',
            'customer_segment' => 'required|string|max:50',
            'annual_income' => 'required|numeric|min:0',
            'join_date' => 'nullable|string',
        ], [
            'first_name.required' => 'Nama depan wajib diisi',
            'second_name.required' => 'Nama belakang wajib diisi',
            'gender.required' => 'Pilih jenis kelamin',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'city.required' => 'Kota wajib diisi',
            'state.required' => 'Provinsi / State wajib diisi',
            'occupation.required' => 'Pekerjaan wajib diisi',
            'customer_segment.required' => 'Pilih segmen customer',
            'annual_income.required' => 'Pendapatan tahunan wajib diisi',
            'annual_income.numeric' => 'Pendapatan tahunan harus berupa angka',
        ]);

        $updated = $this->csvService->update($customerId, $validated);

        if ($updated) {
            return redirect()->route('dashboard.table')
                ->with('success', "✨ Data customer {$customerId} berhasil diperbarui di file customers.csv!");
        }

        return redirect()->back()
            ->withInput()
            ->with('error', "❌ Gagal memperbarui data customer {$customerId} di file customers.csv.");
    }

    public function destroy(string $id): RedirectResponse
    {
        $deleted = $this->csvService->delete($id);

        if ($deleted) {
            return redirect()->route('dashboard.table')
                ->with('success', "🗑️ Customer {$id} berhasil dihapus dari file customers.csv.");
        }

        return redirect()->route('dashboard.table')
            ->with('error', "❌ Gagal menghapus customer {$id} dari file customers.csv.");
    }
}

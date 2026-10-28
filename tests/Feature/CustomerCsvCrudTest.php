<?php

namespace Tests\Feature;

use App\Services\CustomerCsvService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CustomerCsvCrudTest extends TestCase
{
    protected CustomerCsvService $csvService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->csvService = new CustomerCsvService;
    }

    public function test_dashboard_and_tabel_endpoint_loads_successfully_and_contains_required_markers(): void
    {
        $response = $this->get('/dashboard-and-tabel');

        $response->assertStatus(200);
        $response->assertSee('//dashboard (kosongkan)');
        $response->assertSee('//tabel customer (disini akan ada tabel 10 baris dan ada paginasi per 10 data tabel)');
        $response->assertSee('//tabeltransaction(kosongkan)');
        $response->assertSee('Tabel Data Customer');
        $response->assertSee('10 Baris / Halaman');
    }

    public function test_create_customer_endpoint_shows_form(): void
    {
        $response = $this->get('/create-customer');

        $response->assertStatus(200);
        $response->assertSee('Tambah Customer Baru');
        $response->assertSee('Simpan Customer ke CSV');
    }

    public function test_create_update_and_delete_customer_flow_directly_on_csv(): void
    {
        $testId = 'CTEST999';
        $query = \Mockery::mock(Builder::class);
        $query->shouldReceive('updateOrInsert')->twice()->andReturnTrue();
        $query->shouldReceive('where')->once()->with('customer_id', $testId)->andReturnSelf();
        $query->shouldReceive('delete')->once()->andReturn(1);
        DB::shouldReceive('table')->with('customers')->times(3)->andReturn($query);

        // 1. Pastikan ID test belum ada
        $this->csvService->delete($testId);

        // 2. Simpan customer baru via POST /create-customer
        $postData = [
            'customer_id' => $testId,
            'first_name' => 'Budi',
            'second_name' => 'Santoso',
            'gender' => 'Male',
            'date_of_birth' => '1990-01-15',
            'city' => 'Jakarta Selatan',
            'state' => 'DKI Jakarta',
            'occupation' => 'Salaried',
            'customer_segment' => 'Retail',
            'annual_income' => '750000.50',
            'join_date' => '2026-10-27',
        ];

        $response = $this->post('/create-customer', $postData);
        $response->assertRedirect('/dashboard-and-tabel');
        $response->assertSessionHas('success');

        // Verifikasi langsung di file CSV
        $found = $this->csvService->findById($testId);
        $this->assertNotNull($found, 'Data customer baru harus ditemukan di customers.csv');
        $this->assertEquals('Budi', $found['fisrt_name']);
        $this->assertEquals('Santoso', $found['second_name']);

        // 3. Test edit page GET /update-customer/{id}
        $editResponse = $this->get('/update-customer/'.$testId);
        $editResponse->assertStatus(200);
        $editResponse->assertSee($testId);
        $editResponse->assertSee('Budi');

        // 4. Update data customer via POST /update-customer
        $updateData = [
            'customer_id' => $testId,
            'first_name' => 'Budi Wijaya',
            'second_name' => 'Santoso',
            'gender' => 'Male',
            'date_of_birth' => '1990-01-15',
            'city' => 'Bandung',
            'state' => 'Jawa Barat',
            'occupation' => 'Business Owner',
            'customer_segment' => 'Premium',
            'annual_income' => '950000.00',
            'join_date' => '2026-10-27',
        ];

        $updateResponse = $this->post('/update-customer', $updateData);
        $updateResponse->assertRedirect('/dashboard-and-tabel');
        $updateResponse->assertSessionHas('success');

        // Verifikasi pembaruan di file CSV
        $updatedFound = $this->csvService->findById($testId);
        $this->assertNotNull($updatedFound);
        $this->assertEquals('Budi Wijaya', $updatedFound['fisrt_name']);
        $this->assertEquals('Bandung', $updatedFound['city']);
        $this->assertEquals('Premium', $updatedFound['customer_segment']);

        // 5. Hapus customer test dari CSV agar data tetap bersih
        $deleteResponse = $this->post('/delete-customer/'.$testId);
        $deleteResponse->assertRedirect('/dashboard-and-tabel');

        // Verifikasi customer sudah terhapus dari file CSV
        $deletedFound = $this->csvService->findById($testId);
        $this->assertNull($deletedFound, 'Data customer test harus sudah dihapus dari customers.csv');
    }
}

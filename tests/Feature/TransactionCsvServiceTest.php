<?php

namespace Tests\Feature;

use App\Services\TransactionCsvService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionCsvServiceTest extends TestCase
{
    protected string $filePath;

    protected TransactionCsvService $csvService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filePath = storage_path('app/data/transactions-test-'.uniqid().'.csv');
        $this->csvService = new TransactionCsvService;

        $filePath = new \ReflectionProperty(TransactionCsvService::class, 'filePath');
        $filePath->setValue($this->csvService, $this->filePath);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }

        parent::tearDown();
    }

    public function test_it_creates_reads_updates_and_deletes_transactions(): void
    {
        $syncCall = 0;
        $query = \Mockery::mock(Builder::class);
        $query->shouldReceive('updateOrInsert')
            ->twice()
            ->withArgs(function (array $keys, array $values) use (&$syncCall): bool {
                $syncCall++;
                $this->assertArrayHasKey('updated_at', $values);
                $this->assertInstanceOf(\DateTimeInterface::class, $values['updated_at']);

                if ($syncCall === 1) {
                    $this->assertArrayHasKey('created_at', $values);
                    $this->assertInstanceOf(\DateTimeInterface::class, $values['created_at']);
                } else {
                    $this->assertArrayNotHasKey('created_at', $values);
                }

                return true;
            })
            ->andReturnTrue();
        $query->shouldReceive('where')->once()->with('id', 1)->andReturnSelf();
        $query->shouldReceive('delete')->once()->andReturn(1);
        DB::shouldReceive('table')->with('transactions')->times(3)->andReturn($query);

        $transaction = $this->csvService->create([
            'bulan' => 'Maret',
            'minggu_1_pendapatan' => 100,
            'minggu_1_pengeluaran' => 25,
            'minggu_2_pendapatan' => 200,
            'minggu_2_pengeluaran' => 50,
            'minggu_3_pendapatan' => 300,
            'minggu_3_pengeluaran' => 75,
            'minggu_4_pendapatan' => 400,
            'minggu_4_pengeluaran' => 100,
        ]);

        $this->assertSame('1', $transaction['id']);
        $this->assertSame(1000.0, $transaction['pendapatan']);
        $this->assertSame(250.0, $transaction['pengeluaran']);
        $this->assertSame(1000.0, $this->csvService->findById('1')['pendapatan']);
        $this->assertSame(1000.0, $this->csvService->getSummaryStats()['totalPendapatan']);

        $updated = $this->csvService->update('1', [
            'bulan' => 'April',
            'minggu_1_pendapatan' => 10,
            'minggu_1_pengeluaran' => 2,
            'minggu_2_pendapatan' => 20,
            'minggu_2_pengeluaran' => 4,
            'minggu_3_pendapatan' => 30,
            'minggu_3_pengeluaran' => 6,
            'minggu_4_pendapatan' => 40,
            'minggu_4_pengeluaran' => 8,
        ]);

        $this->assertTrue($updated);
        $this->assertSame('April', $this->csvService->findById('1')['bulan']);
        $this->assertSame(100.0, $this->csvService->findById('1')['pendapatan']);
        $this->assertSame(20.0, $this->csvService->findById('1')['pengeluaran']);

        $this->assertTrue($this->csvService->delete('1'));
        $this->assertNull($this->csvService->findById('1'));
        $this->assertFalse($this->csvService->delete('1'));
    }
}

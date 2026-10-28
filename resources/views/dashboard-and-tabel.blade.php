@extends('layouts.app')

@section('title', 'Dashboard & Tabel Customer')

@push('styles')
<style>
    .hero-banner {
        background: linear-gradient(135deg, #00AA13 0%, #00880D 50%, #02660A 100%);
        border-radius: var(--radius-lg);
        padding: 30px 36px;
        color: white;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        position: relative;
        overflow: hidden;
        border-bottom: 6px solid #005F09;
        box-shadow: 0 10px 25px -5px rgba(0, 170, 19, 0.3);
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .hero-text h1 {
        font-size: 1.85rem;
        font-weight: 800;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .hero-text p {
        font-size: 0.95rem;
        opacity: 0.92;
        max-width: 650px;
    }

    .hero-streak-badge {
        background: rgba(255, 255, 255, 0.18);
        border: 2px solid rgba(255, 255, 255, 0.35);
        backdrop-filter: blur(10px);
        padding: 12px 20px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        gap: 14px;
        border-bottom: 4px solid rgba(0, 0, 0, 0.2);
    }

    .streak-icon {
        font-size: 2rem;
        animation: bounce-icon 2s infinite ease-in-out;
    }

    @keyframes bounce-icon {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 30px;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .analytics-card {
        min-width: 0;
        background: #FFFFFF;
        border: 2px solid var(--border-card);
        border-radius: var(--radius-lg);
        padding: 22px;
        box-shadow: 0 5px 0 #E2E8F0;
    }

    .analytics-card-wide {
        grid-column: 1 / -1;
    }

    .analytics-heading {
        margin-bottom: 18px;
    }

    .analytics-heading h3 {
        margin: 0;
        color: #0F172A;
        font-size: 1.12rem;
        font-weight: 800;
    }

    .analytics-heading p {
        margin: 4px 0 0;
        color: #64748B;
        font-size: 0.86rem;
    }

    .chart-frame {
        position: relative;
        height: 300px;
    }

    .customer-chart-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .customer-chart-grid h4 {
        margin: 0 0 12px;
        color: #334155;
        font-size: 0.92rem;
    }

    .customer-chart-frame {
        position: relative;
        height: 260px;
    }

    @media (max-width: 760px) {
        .analytics-grid,
        .customer-chart-grid {
            grid-template-columns: 1fr;
        }

        .analytics-card-wide {
            grid-column: auto;
        }

        .chart-frame {
            height: 260px;
        }
    }

    .stat-card {
        background: white;
        border: 2.5px solid var(--border-card);
        border-radius: var(--radius-md);
        padding: 18px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 0 #E2E8F0;
        transition: transform 0.15s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        border-bottom: 3px solid rgba(0, 0, 0, 0.15);
    }

    .stat-info .stat-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-info .stat-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
    }

    /* Section Placeholder Styles (Kosongkan) */
    .placeholder-module {
        background: #F8FAFC;
        border: 2.5px dashed #CBD5E1;
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .placeholder-badge {
        font-family: monospace;
        font-size: 0.82rem;
        font-weight: 700;
        background: #E2E8F0;
        color: #475569;
        padding: 4px 10px;
        border-radius: 8px;
    }

    /* Filter & Search Bar */
    .filter-bar {
        background: #FFFFFF;
        border: 2.5px solid var(--border-card);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 0 #E2E8F0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .filter-inputs {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .search-group {
        flex: 1;
        min-width: 260px;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 11px 16px 11px 42px;
        border: 2px solid var(--border-card);
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: 0.92rem;
        font-weight: 600;
        background: #F8FAFC;
        color: var(--text-main);
        outline: none;
        transition: all 0.15s ease;
    }

    .search-input:focus {
        background: white;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 170, 19, 0.15);
    }

    .search-icon-inside {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        pointer-events: none;
    }

    .select-filter {
        padding: 11px 16px;
        border: 2px solid var(--border-card);
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: 0.92rem;
        font-weight: 600;
        background: #F8FAFC;
        color: var(--text-main);
        outline: none;
        cursor: pointer;
        min-width: 150px;
    }

    .select-filter:focus {
        border-color: var(--primary);
        background: white;
    }

    /* Table Component */
    .table-container {
        background: white;
        border: 2.5px solid var(--border-card);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .table-header-bar {
        padding: 20px 24px;
        border-bottom: 2px solid var(--border-card);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .customer-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .customer-table th {
        background: #F8FAFC;
        color: #475569;
        font-family: 'Outfit', sans-serif;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 2px solid var(--border-card);
        white-space: nowrap;
    }

    .customer-table td {
        padding: 14px 18px;
        font-size: 0.9rem;
        border-bottom: 1.5px solid #F1F5F9;
        vertical-align: middle;
    }

    .customer-table tr:hover td {
        background-color: #F8FDF9;
    }

    .customer-id-pill {
        font-family: 'Outfit', monospace;
        font-weight: 800;
        font-size: 0.85rem;
        background: #F1F5F9;
        color: #334155;
        padding: 4px 8px;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        display: inline-block;
    }

    .customer-name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00AA13, #58CC02);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        flex-shrink: 0;
        box-shadow: 0 2px 0 rgba(0, 0, 0, 0.15);
    }

    .income-text {
        font-family: 'Outfit', monospace;
        font-weight: 700;
        color: #0F172A;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Duolingo Pagination */
    .pagination-bar {
        padding: 18px 24px;
        background: #F8FAFC;
        border-top: 2px solid var(--border-card);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .pagination-info {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .page-btn {
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.85rem;
        font-weight: 800;
        border-radius: 10px;
        text-decoration: none;
        color: #475569;
        background: white;
        border: 2px solid #E2E8F0;
        border-bottom: 3px solid #CBD5E1;
        transition: all 0.1s ease;
    }

    .page-btn:hover:not(.disabled):not(.active) {
        background: #F1F5F9;
        color: #0F172A;
    }

    .page-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        border-bottom: 3px solid var(--primary-dark);
    }

    .page-btn.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #F8FAFC;
    }

    .jump-form {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .jump-input {
        width: 55px;
        padding: 6px 8px;
        border: 2px solid var(--border-card);
        border-radius: 8px;
        font-weight: 800;
        text-align: center;
        outline: none;
    }

    .jump-input:focus {
        border-color: var(--primary);
    }

    /* Modal Hapus */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 100;
        padding: 20px;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: var(--radius-lg);
        border: 3px solid var(--border-card);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        max-width: 440px;
        width: 100%;
        padding: 28px;
        text-align: center;
        animation: popModal 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes modalPop {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endpush

@section('content')

    <div class="hero-banner">
        <div class="hero-text">
            <h1>
                <span>Halo Sobat FinTrack!</span> 
                <span class="streak-icon">🦉</span>
            </h1>
            <p>Kelola data customer dan transaksi keuangan dengan antarmuka ceria, cepat, dan terpercaya. Data tersinkronisasi langsung dengan file CSV!</p>
        </div>
        <div class="hero-streak-badge">
            <div style="font-size: 1.8rem;">⚡</div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; opacity: 0.9;">Mode Operasi</div>
                <div style="font-size: 1.15rem; font-weight: 800;">CSV CRUD Engine</div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap" style="background: #E8F8EE; color: #00AA13;">👥</div>
            <div class="stat-info">
                <div class="stat-label">Total Customer</div>
                <div class="stat-value">{{ number_format($stats['total_customers'] ?? 0) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap" style="background: #FEF3C7; color: #D97706;">🏙️</div>
            <div class="stat-info">
                <div class="stat-label">Sebaran Kota</div>
                <div class="stat-value">{{ $stats['total_cities'] ?? 0 }} Kota</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap" style="background: #E0F2FE; color: #0284C7;">⭐</div>
            <div class="stat-info">
                <div class="stat-label">Segmen Retail & SME</div>
                <div class="stat-value">{{ number_format(($stats['segments']['Retail'] ?? 0) + ($stats['segments']['SME'] ?? 0)) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap" style="background: #F3E8FF; color: #9333EA;">💰</div>
            <div class="stat-info">
                <div class="stat-label">Rata-rata Pendapatan</div>
                <div class="stat-value">Rp {{ number_format($stats['avg_income'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <section class="analytics-grid" aria-labelledby="analytics-title">
        <div class="analytics-card">
            <div class="analytics-heading">
                <h3 id="analytics-title">1. Pendapatan & Pengeluaran</h3>
                <p>Perbandingan nilai per bulan dari data transaksi.</p>
            </div>
            <div class="chart-frame">
                <canvas id="chartKeuangan" role="img" aria-label="Grafik pendapatan dan pengeluaran per bulan"></canvas>
            </div>
        </div>

        <div class="analytics-card">
            <div class="analytics-heading">
                <h3>2. Margin Laba</h3>
                <p>Persentase laba terhadap pendapatan per bulan.</p>
            </div>
            <div class="chart-frame">
                <canvas id="chartMargin" role="img" aria-label="Grafik margin laba per bulan"></canvas>
            </div>
        </div>

        <div class="analytics-card analytics-card-wide">
            <div class="analytics-heading">
                <h3>3. Informasi Customer</h3>
                <p>
                    {{ number_format($stats['total_customers'] ?? 0) }} customer
                    &middot; {{ $stats['total_cities'] ?? 0 }} kota
                    &middot; rata-rata pendapatan tahunan Rp {{ number_format($stats['avg_income'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="customer-chart-grid">
                <div>
                    <h4>Segmen</h4>
                    <div class="customer-chart-frame">
                        <canvas id="chartSegmen" role="img" aria-label="Distribusi customer berdasarkan segmen"></canvas>
                    </div>
                </div>
                <div>
                    <h4>Gender</h4>
                    <div class="customer-chart-frame">
                        <canvas id="chartGender" role="img" aria-label="Distribusi customer berdasarkan gender"></canvas>
                    </div>
                </div>
                <div>
                    <h4>5 Kota dengan Customer Terbanyak</h4>
                    <div class="customer-chart-frame">
                        <canvas id="chartKota" role="img" aria-label="Lima kota dengan customer terbanyak"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="section-tabel-customer">
        <div class="filter-bar">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2 style="font-size: 1.35rem; color: #0F172A; display: flex; align-items: center; gap: 8px;">
                        <span>📋</span>
                        <span>Tabel Data Customer</span>
                        <span style="font-size: 0.75rem; background: #00AA13; color: white; padding: 2px 8px; border-radius: 6px; font-weight: 700;">10 Baris / Halaman</span>
                    </h2>
                    <p style="font-size: 0.84rem; color: var(--text-muted); margin-top: 2px;">
                        Sumber data: <code>storage/app/data/customers.csv</code>
                    </p>
                </div>
                <a href="{{ route('customers.create') }}" class="btn-3d btn-primary">
                    <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
                    <span>+ Tambah Customer</span>
                </a>
            </div>

            <form action="{{ route('dashboard.table') }}" method="GET" class="filter-inputs">
                <div class="search-group">
                    <i data-lucide="search" class="search-icon-inside" style="width: 18px; height: 18px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Cari ID, Nama, Kota, atau Pekerjaan...">
                </div>

                <select name="segment" class="select-filter" onchange="this.form.submit()">
                    <option value="">Semua Segmen</option>
                    @foreach(['Retail', 'SME', 'Premium', 'Corporate', 'Wealth'] as $segOption)
                        <option value="{{ $segOption }}" {{ request('segment') === $segOption ? 'selected' : '' }}>
                            Segmen: {{ $segOption }}
                        </option>
                    @endforeach
                </select>

                <select name="gender" class="select-filter" onchange="this.form.submit()">
                    <option value="">Semua Gender</option>
                    <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Laki-laki (Male)</option>
                    <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Perempuan (Female)</option>
                </select>

                <button type="submit" class="btn-3d btn-secondary" style="padding: 10px 16px;">
                    <i data-lucide="filter" style="width: 16px; height: 16px;"></i>
                    <span>Terapkan</span>
                </button>

                @if(request('search') || request('segment') || request('gender'))
                    <a href="{{ route('dashboard.table') }}" class="btn-3d btn-secondary" style="padding: 10px 14px; color: #EF4444;" title="Reset Filter">
                        <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th>ID Customer</th>
                            <th>Nama Lengkap</th>
                            <th>Gender</th>
                            <th>Tgl Lahir</th>
                            <th>Kota & Provinsi</th>
                            <th>Pekerjaan</th>
                            <th>Segmen</th>
                            <th>Annual Income</th>
                            <th>Tgl Gabung</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td><span class="customer-id-pill">{{ $customer['customer_id'] ?? '-' }}</span></td>
                                <td>
                                    <div class="customer-name-cell">
                                        <div class="avatar-circle">
                                            {{ strtoupper(substr($customer['first_name'] ?? 'C', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 800; color: #0F172A;">
                                                {{ $customer['full_name'] ?? ($customer['first_name'] . ' ' . $customer['second_name']) }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748B;">
                                                {{ $customer['first_name'] ?? '' }} (Depan)
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(strcasecmp($customer['gender'] ?? '', 'Male') === 0)
                                        <span class="badge-gender-male">👨 Male</span>
                                    @else
                                        <span class="badge-gender-female">👩 Female</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;"><span style="font-family: monospace; font-size: 0.85rem; color: #334155;">{{ $customer['date_of_birth'] ?? '-' }}</span></td>
                                <td>
                                    <div style="font-weight: 700; color: #1E293B;">{{ $customer['city'] ?? '-' }}</div>
                                    <div style="font-size: 0.76rem; color: #64748B;">{{ $customer['state'] ?? '-' }}</div>
                                </td>
                                <td><span style="font-weight: 600; color: #334155;">{{ $customer['occupation'] ?? '-' }}</span></td>
                                <td>
                                    @php
                                        $seg = strtolower($customer['customer_segment'] ?? '');
                                        $badgeClass = match($seg) {
                                            'retail' => 'badge-retail',
                                            'sme' => 'badge-sme',
                                            'premium' => 'badge-premium',
                                            'corporate' => 'badge-corporate',
                                            'wealth' => 'badge-wealth',
                                            default => 'badge-retail',
                                        };
                                        $segIcon = match($seg) {
                                            'retail' => '🛒',
                                            'sme' => '🏢',
                                            'premium' => '⭐',
                                            'corporate' => '🏛️',
                                            'wealth' => '💎',
                                            default => '🏷️',
                                        };
                                    @endphp
                                    <span class="badge-segment {{ $badgeClass }}">
                                        <span>{{ $segIcon }}</span>
                                        <span>{{ $customer['customer_segment'] ?? 'Retail' }}</span>
                                    </span>
                                </td>
                                <td><span class="income-text">Rp {{ number_format((float) ($customer['annual_income'] ?? 0), 2, ',', '.') }}</span></td>
                                <td style="white-space: nowrap;"><span style="font-family: monospace; font-size: 0.85rem; color: #475569;">{{ $customer['join_date'] ?? '-' }}</span></td>
                                <td>
                                    <div class="action-group" style="justify-content: center;">
                                        <a href="{{ route('customers.edit', $customer['customer_id']) }}" class="btn-3d btn-secondary btn-sm" title="Edit customer {{ $customer['customer_id'] }}">
                                            <i data-lucide="edit-3" style="width: 14px; height: 14px; color: #0284C7;"></i>
                                            <span>Edit</span>
                                        </a>
                                        <button type="button" onclick="openDeleteModal('{{ $customer['customer_id'] }}', '{{ addslashes($customer['full_name']) }}')" class="btn-3d btn-secondary btn-sm" style="color: #EF4444;">
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 48px 20px;">
                                    <div style="font-size: 3rem; margin-bottom: 12px;">🔍</div>
                                    <h3 style="font-size: 1.15rem; color: #1E293B; margin-bottom: 6px;">Tidak ada data customer yang cocok</h3>
                                    <a href="{{ route('dashboard.table') }}" class="btn-3d btn-secondary btn-sm">Reset Filter</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-bar">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $customers->firstItem() ?? 0 }}</strong> - <strong>{{ $customers->lastItem() ?? 0 }}</strong> dari <strong>{{ number_format($customers->total()) }}</strong> data customer
                </div>

                <div class="pagination-controls">
                    @if($customers->currentPage() > 1)
                        <a href="{{ $customers->appends(request()->query())->url(1) }}" class="page-btn">&laquo;</a>
                    @else
                        <span class="page-btn disabled">&laquo;</span>
                    @endif

                    @if($customers->onFirstPage())
                        <span class="page-btn disabled"><i data-lucide="chevron-left" style="width: 14px; height: 14px;"></i></span>
                    @else
                        <a href="{{ $customers->appends(request()->query())->previousPageUrl() }}" class="page-btn"><i data-lucide="chevron-left" style="width: 14px; height: 14px;"></i></a>
                    @endif

                    @php
                        $current = $customers->currentPage();
                        $last = $customers->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $customers->appends(request()->query())->url(1) }}" class="page-btn">1</a>
                        @if($start > 2)
                            <span class="page-btn disabled" style="border: none; background: transparent;">...</span>
                        @endif
                    @endif

                    @for($i = $start; $i <= $end; $i++)
                        @if($i == $current)
                            <span class="page-btn active">{{ $i }}</span>
                        @else
                            <a href="{{ $customers->appends(request()->query())->url($i) }}" class="page-btn">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="page-btn disabled" style="border: none; background: transparent;">...</span>
                        @endif
                        <a href="{{ $customers->appends(request()->query())->url($last) }}" class="page-btn">{{ $last }}</a>
                    @endif
                    
                    @if($customers->hasMorePages())
                        <a href="{{ $customers->appends(request()->query())->nextPageUrl() }}" class="page-btn">
                            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                        </a>
                    @else
                        <span class="page-btn disabled">
                            <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                        </span>
                    @endif

                    @if($customers->currentPage() < $customers->lastPage())
                        <a href="{{ $customers->appends(request()->query())->url($customers->lastPage()) }}" class="page-btn" title="Halaman Terakhir">
                            &raquo;
                        </a>
                    @else
                        <span class="page-btn disabled">&raquo;</span>
                    @endif
                </div>

                <form action="{{ route('dashboard.table') }}" method="GET" class="jump-form">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('segment')) <input type="hidden" name="segment" value="{{ request('segment') }}"> @endif
                    @if(request('gender')) <input type="hidden" name="gender" value="{{ request('gender') }}"> @endif
                    <span>Lompat ke:</span>
                    <input type="number" name="page" min="1" max="{{ $customers->lastPage() }}" placeholder="{{ $customers->currentPage() }}" class="jump-input">
                    <button type="submit" class="btn-3d btn-secondary btn-sm" style="padding: 4px 10px;">Go</button>
                </form>
            </div>
        </div>
    </section>

    <section id="section-tabel-transaksi">
        @include('transaction.index')
    </section>

    <div id="custom-delete-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
        <div style="background: white; border: 2.5px solid #E2E8F0; border-radius: 20px; box-shadow: 0 12px 30px rgba(0,0,0,0.2); width: 100%; max-width: 420px; padding: 28px; text-align: center; animation: modalPop 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            
            <div style="width: 70px; height: 70px; background: #FEF2F2; border: 2px solid #FCA5A5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; font-size: 2rem;">
                🗑️
            </div>

            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin-bottom: 8px;">
                Konfirmasi Hapus Data
            </h3>
            
            <p id="modal-delete-text" style="font-size: 0.92rem; color: #64748B; margin-bottom: 24px; line-height: 1.5;">
                Apakah Anda yakin ingin menghapus data ini?
            </p>

            <form id="custom-delete-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="custom-delete-method" value="DELETE">
                
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button type="button" onclick="closeDeleteModal()" 
                            style="flex: 1; padding: 10px 18px; background: #F1F5F9; color: #475569; border: 2px solid #E2E8F0; border-bottom: 3.5px solid #CBD5E1; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.9rem;">
                        Batal
                    </button>
                    
                    <button type="submit" 
                            style="flex: 1; padding: 10px 18px; background: #EF4444; color: white; border: none; border-bottom: 3.5px solid #DC2626; border-radius: 12px; font-weight: 800; cursor: pointer; font-size: 0.9rem;">
                        Ya, Hapus!
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openDeleteModal(id, name) {
        let modal = document.getElementById('custom-delete-modal');
        let form = document.getElementById('custom-delete-form');
        let text = document.getElementById('modal-delete-text');
        let method = document.getElementById('custom-delete-method');

        text.innerHTML = 'Apakah Anda yakin ingin menghapus customer <strong style="color: #0F172A;">' + name + ' (' + id + ')</strong>? Data tidak bisa dikembalikan.';
        form.action = '/delete-customer/' + id;
        method.value = 'POST';

        modal.style.display = 'flex';
    }

    function openDeleteTransaksiModal(id, bulan) {
        let modal = document.getElementById('custom-delete-modal');
        let form = document.getElementById('custom-delete-form');
        let text = document.getElementById('modal-delete-text');
        let method = document.getElementById('custom-delete-method');

        text.innerHTML = 'Apakah Anda yakin ingin menghapus data transaksi bulan <strong style="color: #0F172A;">' + bulan + '</strong>? Data tidak bisa dikembalikan.';
        form.action = '/transaction/' + id;
        method.value = 'DELETE';

        modal.style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('custom-delete-modal').style.display = 'none';
    }

    window.onclick = function(event) {
        let modal = document.getElementById('custom-delete-modal');
        if (event.target == modal) {
            closeDeleteModal();
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const transactionLabels = @json($transactionChart['labels'] ?? []);
    const customerSegments = @json(array_keys($customerChartStats['segments'] ?? []));
    const customerSegmentCounts = @json(array_values($customerChartStats['segments'] ?? []));
    const customerGenders = @json(array_keys($customerChartStats['genders'] ?? []));
    const customerGenderCounts = @json(array_values($customerChartStats['genders'] ?? []));
    const customerCities = @json(array_keys($customerChartStats['top_cities'] ?? []));
    const customerCityCounts = @json(array_values($customerChartStats['top_cities'] ?? []));
    const chartColors = ['#00AA13', '#F97316', '#0284C7', '#9333EA', '#E11D48', '#D97706'];
    const currencyFormatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    });

    new Chart(document.getElementById('chartKeuangan'), {
        type: 'bar',
        data: {
            labels: transactionLabels,
            datasets: [
                {
                    label: 'Pendapatan',
                    data: @json($transactionChart['income'] ?? []),
                    backgroundColor: 'rgba(0, 170, 19, 0.78)',
                    borderColor: '#00880D',
                    borderWidth: 1,
                    borderRadius: 6
                },
                {
                    label: 'Pengeluaran',
                    data: @json($transactionChart['expenses'] ?? []),
                    backgroundColor: 'rgba(239, 68, 68, 0.78)',
                    borderColor: '#DC2626',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => currencyFormatter.format(value) }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: { label: context => `${context.dataset.label}: ${currencyFormatter.format(context.raw)}` }
                }
            }
        }
    });

    new Chart(document.getElementById('chartMargin'), {
        type: 'line',
        data: {
            labels: transactionLabels,
            datasets: [{
                label: 'Margin laba',
                data: @json($transactionChart['margin'] ?? []),
                borderColor: '#7C3AED',
                backgroundColor: 'rgba(124, 58, 237, 0.12)',
                pointBackgroundColor: '#7C3AED',
                pointRadius: 4,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    ticks: { callback: value => `${value}%` }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: { label: context => `${context.dataset.label}: ${Number(context.raw).toFixed(1)}%` }
                }
            }
        }
    });

    new Chart(document.getElementById('chartSegmen'), {
        type: 'doughnut',
        data: {
            labels: customerSegments,
            datasets: [{
                data: customerSegmentCounts,
                backgroundColor: customerSegments.map((_, index) => chartColors[index % chartColors.length]),
                borderWidth: 2,
                borderColor: '#FFFFFF'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('chartGender'), {
        type: 'doughnut',
        data: {
            labels: customerGenders,
            datasets: [{
                data: customerGenderCounts,
                backgroundColor: customerGenders.map((_, index) => ['#0284C7', '#E11D48', '#94A3B8'][index % 3]),
                borderWidth: 2,
                borderColor: '#FFFFFF'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('chartKota'), {
        type: 'bar',
        data: {
            labels: customerCities,
            datasets: [{
                label: 'Jumlah customer',
                data: customerCityCounts,
                backgroundColor: 'rgba(2, 132, 199, 0.78)',
                borderColor: '#0369A1',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush

@extends('layouts.app')

@section('title', 'Perbarui Data Customer (CSV)')

@push('styles')
<style>
    .form-wrapper {
        max-width: 840px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border: 2.5px solid var(--border-card);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        padding: 32px 36px;
    }

    .form-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 24px;
        margin-bottom: 28px;
        border-bottom: 2px solid var(--border-card);
    }

    .mascot-icon-circle {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #1CB0F6, #0284C7);
        border-bottom: 4px solid #0369A1;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        box-shadow: 0 4px 0 #0369A1;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.col-span-2 {
        grid-column: span 2;
    }

    .form-label {
        font-family: 'Outfit', sans-serif;
        font-size: 0.88rem;
        font-weight: 800;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label .req {
        color: var(--duo-red);
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--border-card);
        border-radius: var(--radius-md);
        font-family: inherit;
        font-size: 0.95rem;
        font-weight: 600;
        background: #F8FAFC;
        color: var(--text-main);
        outline: none;
        transition: all 0.15s ease;
    }

    .form-control:focus {
        background: white;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 170, 19, 0.15);
    }

    .form-control.is-invalid {
        border-color: var(--duo-red);
        background: #FEF2F2;
    }

    .form-control:read-only {
        background: #F1F5F9;
        color: #64748B;
        cursor: not-allowed;
    }

    .error-msg {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--duo-red);
    }

    .radio-card-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .radio-card {
        border: 2px solid var(--border-card);
        border-bottom: 3.5px solid #CBD5E1;
        border-radius: var(--radius-md);
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: 700;
        background: #F8FAFC;
        transition: all 0.12s ease;
    }

    .radio-card:hover {
        background: white;
        border-color: #00AA13;
    }

    .radio-card input[type="radio"] {
        accent-color: #00AA13;
        width: 18px;
        height: 18px;
    }

    .radio-card.selected {
        border-color: #00AA13;
        border-bottom-color: #00880D;
        background: #E8F8EE;
        color: #00880D;
    }

    .segment-picker {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 10px;
    }

    .segment-card {
        border: 2px solid var(--border-card);
        border-bottom: 3.5px solid #CBD5E1;
        border-radius: var(--radius-md);
        padding: 10px;
        text-align: center;
        cursor: pointer;
        background: #F8FAFC;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.12s ease;
    }

    .segment-card:hover {
        border-color: #00AA13;
        background: white;
    }

    .segment-card.active {
        border-color: #00AA13;
        border-bottom-color: #00880D;
        background: #E8F8EE;
        color: #00880D;
    }

    .form-footer {
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid var(--border-card);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    @media (max-width: 640px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .form-group.col-span-2 {
            grid-column: span 1;
        }
        .segment-picker {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
@php
    $formatDateForInput = function($val) {
        if (!$val) return '';
        try {
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $val)) {
                return \Carbon\Carbon::createFromFormat('d-m-Y', $val)->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return $val;
        }
    };
    $dobInput = $formatDateForInput($customer['date_of_birth'] ?? '');
    $joinInput = $formatDateForInput($customer['join_date'] ?? '');
    $activeGender = old('gender', $customer['gender'] ?? 'Male');
    $activeSegment = old('customer_segment', $customer['customer_segment'] ?? 'Retail');
@endphp

<div class="form-wrapper">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('dashboard.table') }}" class="btn-3d btn-secondary btn-sm" style="display: inline-flex;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            <span>Kembali ke Dashboard & Tabel</span>
        </a>
    </div>

    <div class="form-card">
        <!-- Form Header -->
        <div class="form-header">
            <div class="mascot-icon-circle">
                ✏️
            </div>
            <div>
                <h1 style="font-size: 1.6rem; color: #0F172A; line-height: 1.2;">
                    Update Data Customer
                </h1>
                <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 4px;">
                    Endpoint: <code>/update-customer</code> &bull; Mengubah data customer <strong>{{ $customer['customer_id'] }}</strong> di file <code>storage/app/data/customers.csv</code>
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="toast-banner toast-error" style="margin-bottom: 24px;">
                <div>
                    <strong>Mohon lengkapi formulir dengan benar:</strong>
                    <ul style="margin-top: 6px; padding-left: 20px; font-weight: 600; font-size: 0.85rem;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('customers.update') }}" method="POST" id="updateCustomerForm">
            @csrf

            <!-- Hidden / Locked Customer ID -->
            <input type="hidden" name="customer_id" value="{{ $customer['customer_id'] }}">

            <div class="form-grid">
                <!-- Customer ID Info -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Customer ID</span>
                        <span style="font-size: 0.75rem; color: #64748B;">(Primary Key CSV)</span>
                    </label>
                    <input type="text" 
                           value="{{ $customer['customer_id'] }}" 
                           class="form-control" 
                           style="font-family: monospace; font-weight: 800;"
                           readonly>
                    <span style="font-size: 0.75rem; color: #64748B;">ID unik pelanggan dari data CSV (tidak dapat diubah).</span>
                </div>

                <!-- Gender -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Jenis Kelamin (Gender)</span>
                        <span class="req">*</span>
                    </label>
                    <div class="radio-card-group">
                        <label class="radio-card {{ strcasecmp($activeGender, 'Male') === 0 ? 'selected' : '' }}" onclick="selectGender('Male')">
                            <input type="radio" name="gender" value="Male" {{ strcasecmp($activeGender, 'Male') === 0 ? 'checked' : '' }}>
                            <span>👨 Laki-laki (Male)</span>
                        </label>
                        <label class="radio-card {{ strcasecmp($activeGender, 'Female') === 0 ? 'selected' : '' }}" onclick="selectGender('Female')">
                            <input type="radio" name="gender" value="Female" {{ strcasecmp($activeGender, 'Female') === 0 ? 'checked' : '' }}>
                            <span>👩 Perempuan (Female)</span>
                        </label>
                    </div>
                </div>

                <!-- First Name -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Nama Depan (First Name)</span>
                        <span class="req">*</span>
                    </label>
                    <input type="text" 
                           name="first_name" 
                           value="{{ old('first_name', $customer['first_name'] ?? ($customer['fisrt_name'] ?? '')) }}" 
                           class="form-control @error('first_name') is-invalid @enderror" 
                           required>
                    @error('first_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- Second Name -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Nama Belakang (Second Name)</span>
                        <span class="req">*</span>
                    </label>
                    <input type="text" 
                           name="second_name" 
                           value="{{ old('second_name', $customer['second_name'] ?? '') }}" 
                           class="form-control @error('second_name') is-invalid @enderror" 
                           required>
                    @error('second_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Tanggal Lahir (Date of Birth)</span>
                        <span class="req">*</span>
                    </label>
                    <input type="date" 
                           name="date_of_birth" 
                           value="{{ old('date_of_birth', $dobInput) }}" 
                           class="form-control @error('date_of_birth') is-invalid @enderror" 
                           required>
                    @error('date_of_birth') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- Join Date -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Tanggal Bergabung (Join Date)</span>
                    </label>
                    <input type="date" 
                           name="join_date" 
                           value="{{ old('join_date', $joinInput) }}" 
                           class="form-control @error('join_date') is-invalid @enderror">
                    <span style="font-size: 0.75rem; color: #64748B;">Format di CSV: {{ $customer['join_date'] ?? '-' }}</span>
                </div>

                <!-- City -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Kota (City)</span>
                        <span class="req">*</span>
                    </label>
                    <input type="text" 
                           name="city" 
                           value="{{ old('city', $customer['city'] ?? '') }}" 
                           class="form-control @error('city') is-invalid @enderror" 
                           required>
                    @error('city') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- State -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Provinsi / Wilayah (State)</span>
                        <span class="req">*</span>
                    </label>
                    <input type="text" 
                           name="state" 
                           value="{{ old('state', $customer['state'] ?? '') }}" 
                           class="form-control @error('state') is-invalid @enderror" 
                           required>
                    @error('state') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- Occupation -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Pekerjaan (Occupation)</span>
                        <span class="req">*</span>
                    </label>
                    <select name="occupation" class="form-control @error('occupation') is-invalid @enderror" required>
                        @foreach($occupations as $occ)
                            <option value="{{ $occ }}" {{ old('occupation', $customer['occupation'] ?? '') === $occ ? 'selected' : '' }}>
                                {{ $occ }}
                            </option>
                        @endforeach
                    </select>
                    @error('occupation') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- Annual Income -->
                <div class="form-group">
                    <label class="form-label">
                        <span>Pendapatan Tahunan (Annual Income)</span>
                        <span class="req">*</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="annual_income" 
                           value="{{ old('annual_income', (float)($customer['annual_income'] ?? 0)) }}" 
                           class="form-control @error('annual_income') is-invalid @enderror" 
                           required>
                    @error('annual_income') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <!-- Customer Segment (Pill Cards) -->
                <div class="form-group col-span-2">
                    <label class="form-label">
                        <span>Segmen Customer</span>
                        <span class="req">*</span>
                    </label>
                    <input type="hidden" name="customer_segment" id="selectedSegment" value="{{ $activeSegment }}">
                    <div class="segment-picker">
                        @php
                            $segIcons = ['Retail' => '🛒', 'SME' => '🏢', 'Premium' => '⭐', 'Corporate' => '🏛️', 'Wealth' => '💎'];
                        @endphp
                        @foreach($segments as $seg)
                            <div class="segment-card {{ strcasecmp($activeSegment, $seg) === 0 ? 'active' : '' }}" 
                                 onclick="selectSegment('{{ $seg }}')">
                                <div style="font-size: 1.4rem; margin-bottom: 4px;">{{ $segIcons[$seg] ?? '🏷️' }}</div>
                                <div>{{ $seg }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="form-footer">
                <a href="{{ route('dashboard.table') }}" class="btn-3d btn-secondary">
                    Batal & Kembali
                </a>
                <button type="submit" class="btn-3d btn-primary" style="padding: 12px 30px; font-size: 1rem;">
                    <i data-lucide="check" style="width: 20px; height: 20px;"></i>
                    <span>Simpan Perubahan ke CSV</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function selectGender(val) {
        document.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected'));
        event.currentTarget.classList.add('selected');
    }

    function selectSegment(val) {
        document.getElementById('selectedSegment').value = val;
        document.querySelectorAll('.segment-card').forEach(c => c.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
</script>
@endpush

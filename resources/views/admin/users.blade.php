@extends('layouts.app')
@section('title', 'Manajemen Pengguna - Admin')

@section('content')
<div class="bg-dark text-white py-4 border-bottom shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0"><i class="fa fa-users-gear me-2"></i> Database Pengguna</h4>
            <p class="text-white-50 mb-0 small">Kendalikan akses dan moderasi akun mahasiswa Telkom.</p>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        
        <!-- SIDEBAR MENU -->
        <div class="col-lg-3">
            @include('layouts.partials.admin_sidebar')
        </div>

        <!-- MAIN TABLE -->
        <div class="col-lg-9">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" data-bs-dismiss="modal"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0 bg-white">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">PENGGUNA / NIM</th>
                                <th class="py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">EMAIL</th>
                                <th class="py-3 text-center" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">HAK AKSES</th>
                                <th class="text-end pe-4 py-3" style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px;">MODERASI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-3 border" style="width: 40px; height: 40px; font-weight: 800;">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0">{{ $u->name }}</div>
                                                <div class="small text-muted">{{ $u->nim ?? 'No NIM' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 small text-muted">{{ $u->email }}</td>
                                    <td class="py-3 text-center">
                                        @php
                                            $badgeClass = match($u->role) {
                                                'admin' => 'bg-dark text-white',
                                                'seller' => 'bg-danger-subtle text-danger border-danger',
                                                default => 'bg-success-subtle text-success border-success'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 border shadow-none" style="font-size: 0.65rem; font-weight: 800;">{{ strtoupper($u->role) }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($u->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="return confirm('Cabut akses dan hapus akun mahasiswa ini?')">
                                                    <i class="fa fa-ban me-1"></i> Banned
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-light text-muted border py-2 px-3">PROTECTED</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

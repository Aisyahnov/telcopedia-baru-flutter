@extends('layouts.app')
@section('title', 'Manajemen Pengguna - Admin')

@section('hero_title', 'Kelola User')
@section('hero_subtitle', 'Kendalikan akses dan moderasi akun mahasiswa Telkom.')
@section('hero_emoji', '')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Akun</h5>
            <p class="text-muted small mb-0">Total {{ count($users) }} mahasiswa terdaftar di Telcopedia.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-20 border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-management">
        <div class="card-body p-0">
            <table class="table table-management table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">PENGGUNA / NIM</th>
                        <th>EMAIL</th>
                        <th class="text-center">HAK AKSES</th>
                        <th class="text-end pe-4">MODERASI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center me-3 border shadow-sm" style="width: 45px; height: 45px; font-weight: 800; font-size: 1.1rem;">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $u->name }}</div>
                                        <div class="x-small text-muted" style="font-size: 0.7rem;">{{ $u->nim ?? 'NIM TIDAK TERSEDIA' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 small text-muted">{{ $u->email }}</td>
                            <td class="py-4 text-center">
                                @php
                                    $badgeClass = match($u->role) {
                                        'admin' => 'bg-dark text-white',
                                        'seller' => 'bg-danger-subtle text-danger border-danger',
                                        default => 'bg-success-subtle text-success border-success'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 border shadow-none" style="font-size: 0.65rem; font-weight: 800;">{{ strtoupper($u->role) }}</span>
                            </td>
                            <td class="text-end pe-4 py-4">
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline ban-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light text-danger rounded-pill px-3 fw-bold border shadow-sm btn-ban">
                                            <i class="fa fa-ban me-1"></i> Banned
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-muted border py-2 px-3 fw-bold" style="font-size: 0.6rem;">MY ACCOUNT</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @push('scripts')
    <script>
        document.querySelectorAll('.btn-ban').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.ban-form');
                Swal.fire({
                    title: 'Banned User?',
                    text: "Akses akun mahasiswa ini akan dicabut permanen!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#9F1521',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Banned!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
@endsection

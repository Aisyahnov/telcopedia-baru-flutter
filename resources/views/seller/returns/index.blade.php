@extends('layouts.app')
@section('title', 'Retur Barang - Telcopedia')

@section('hero_title', 'Retur & Komplain')
@section('hero_subtitle', 'Kelola barang yang dikomplain dan diajukan pengembalian oleh pembeli.')
@section('hero_emoji', '')

@section('content')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Pengajuan Retur</h5>
            <p class="text-muted small mb-0">Kelola keluhan dan permintaan pengembalian barang dari pembeli.</p>
        </div>
    </div>

    <div class="card card-management mb-5">
        <div class="card-body p-0">
            <table class="table table-management table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">PRODUK & PESANAN</th>
                        <th class="text-center">PEMBELI</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $ret)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $ret->product->image_url }}" alt="{{ $ret->product->name }}" class="rounded-15 shadow-sm me-3 border" width="55" height="55" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark mb-1">{{ $ret->product->name }}</div>
                                        <div class="x-small text-muted fw-normal">ORDER: #TPD-{{ $ret->order_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-4">
                                <div class="fw-bold text-dark">{{ $ret->user->name }}</div>
                                <div class="x-small text-muted">ID: #{{ $ret->user_id }}</div>
                            </td>
                            <td class="text-center py-4">
                                @if($ret->status == 'pending')
                                    <span class="badge-status bg-warning-subtle text-warning border border-warning">MENUNGGU</span>
                                @elseif($ret->status == 'approved')
                                    <span class="badge-status bg-success-subtle text-success border border-success">DISETUJUI</span>
                                @else
                                    <span class="badge-status bg-danger-subtle text-danger border border-danger">DITOLAK</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-4 text-nowrap">
                                <button class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#returnDetail{{ $ret->id }}">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Modal Detail Retur -->
                        <div class="modal fade" id="returnDetail{{ $ret->id }}" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-24 border-0 shadow-lg overflow-hidden">
                              <div class="modal-header border-0 p-4 pb-0">
                                <h5 class="modal-title fw-bold text-maroon">Detail Pengajuan Retur</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body p-4 pt-3 text-start">
                                  <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-15 border border-dashed">
                                      <img src="{{ $ret->product->image_url }}" alt="{{ $ret->product->name }}" class="rounded-12 shadow-sm me-3" width="60" height="60" style="object-fit: cover;">
                                      <div class="overflow-hidden">
                                          <h6 class="fw-bold mb-1 text-dark text-truncate">{{ $ret->product->name }}</h6>
                                          <span class="text-muted x-small d-block">ID PESANAN: #TPD-{{ $ret->order_id }}</span>
                                      </div>
                                  </div>
                                  
                                  <div class="row g-4 mb-4">
                                      <div class="col-6">
                                          <span class="x-small text-muted d-block mb-1">Diajukan Oleh</span>
                                          <div class="fw-bold text-dark">{{ $ret->user->name }}</div>
                                      </div>
                                      <div class="col-6 text-end">
                                          <span class="x-small text-muted d-block mb-1">Status Saat Ini</span>
                                          @if($ret->status == 'pending')
                                              <span class="badge-status bg-warning-subtle text-warning border border-warning">PENDING</span>
                                          @elseif($ret->status == 'approved')
                                              <span class="badge-status bg-success-subtle text-success border border-success">DISETUJUI</span>
                                          @else
                                              <span class="badge-status bg-danger-subtle text-danger border border-danger">DITOLAK</span>
                                          @endif
                                      </div>
                                  </div>
                                  
                                  <div class="mb-4">
                                      <span class="x-small text-muted d-block mb-2">Alasan Pengembalian</span>
                                      <div class="p-3 bg-light rounded-15 border text-dark" style="font-size: 0.9rem; line-height: 1.6;">{{ $ret->reason }}</div>
                                  </div>
                                  
                                  @if($ret->media)
                                  <div class="mb-4">
                                      <span class="x-small text-muted d-block mb-2">Lampiran Bukti</span>
                                      @php
                                          $ext = strtolower(pathinfo($ret->media, PATHINFO_EXTENSION));
                                      @endphp
                                      <div class="position-relative">
                                          @if(in_array($ext, ['mp4', 'mov', 'avi']))
                                              <video controls class="w-100 rounded-20 border shadow-sm" style="max-height: 250px;">
                                                  <source src="{{ asset('storage/' . $ret->media) }}" type="video/{{ $ext == 'mov' ? 'quicktime' : $ext }}">
                                              </video>
                                          @else
                                              <img src="{{ asset('storage/' . $ret->media) }}" class="img-fluid rounded-20 border shadow-sm w-100" style="max-height: 250px; object-fit: cover;">
                                          @endif
                                      </div>
                                  </div>
                                  @endif
                                  
                                  @if($ret->status == 'pending')
                                      <div class="d-flex gap-3 pt-2">
                                          <form action="{{ route('seller.returns.approve', $ret->id) }}" method="POST" class="flex-fill">
                                              @csrf
                                              <button class="btn btn-maroon w-100 py-3 shadow-sm"><i class="fa fa-check-circle me-2"></i> Setujui Retur</button>
                                          </form>
                                          <form action="{{ route('seller.returns.reject', $ret->id) }}" method="POST" class="flex-fill">
                                              @csrf
                                              <button class="btn btn-outline-dark w-100 rounded-pill py-3 fw-bold">Tolak</button>
                                          </form>
                                      </div>
                                  @else
                                      <div class="bg-light p-3 rounded-15 text-center text-muted x-small fw-bold">
                                          KEPUTUSAN TELAH DIAMBIL PADA {{ $ret->updated_at->format('d M Y') }}
                                      </div>
                                  @endif
                              </div>
                            </div>
                          </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa fa-arrow-rotate-left fa-4x text-muted opacity-25 mb-4 d-block mx-auto"></i>
                                    <h6 class="fw-bold mb-0">Tidak ada pengajuan retur.</h6>
                                    <p class="text-muted small">Bagus! Semua pembeli puas dengan produk Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
@endsection

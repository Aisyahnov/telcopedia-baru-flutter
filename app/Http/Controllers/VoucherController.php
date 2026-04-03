<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    /**
     * Display a listing of active vouchers.
     */
    public function index()
    {
        // Ambil voucher yang masih aktif (belum expired)
        $vouchers = Voucher::where(function($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', Carbon::today());
        })
        ->latest()
        ->get();

        return view('vouchers.index', compact('vouchers'));
    }
}

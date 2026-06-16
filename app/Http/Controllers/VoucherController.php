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
        $query = Voucher::where(function($q) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', Carbon::today());
        });

        if ($keyword = request('keyword')) {
            $query->where(function($q) use ($keyword) {
                $q->where('code', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%");
            });
        }

        $vouchers = $query->latest()->paginate(10);

        return view('vouchers.index', compact('vouchers'));
    }
}

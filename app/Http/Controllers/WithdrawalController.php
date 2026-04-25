<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\User;

class WithdrawalController extends Controller
{
    public function sellerIndex(Request $request)
    {
        $withdrawals = Withdrawal::where('user_id', $request->user()->id)->latest()->get();
        return view('seller.withdrawals.index', compact('withdrawals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        $user = $request->user();

        if ($user->balance < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
        ]);

        // Deduct balance immediately or on approval? 
        // Usually best to deduct immediately and put in "escrow" or just pending state.
        $user->decrement('balance', $request->amount);

        return back()->with('success', 'Permintaan penarikan dana berhasil dikirim.');
    }

    public function adminIndex()
    {
        $withdrawals = Withdrawal::with('user')->latest()->get();
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') return back();

        $withdrawal->status = 'approved';
        $withdrawal->save();

        return back()->with('success', 'Penarikan dana disetujui.');
    }

    public function reject($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        if ($withdrawal->status !== 'pending') return back();

        $withdrawal->status = 'rejected';
        $withdrawal->save();

        // Refund balance
        $withdrawal->user->increment('balance', $withdrawal->amount);

        return back()->with('success', 'Penarikan dana ditolak. Saldo dikembalikan ke penjual.');
    }
}

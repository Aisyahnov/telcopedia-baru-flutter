<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenarikanDana;
use App\Models\User;

class PenarikanController extends Controller
{
    public function sellerIndex(Request $request)
    {
        $withdrawals = PenarikanDana::where('user_id', $request->user()->id)->latest()->get();
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

        if ($user->saldo < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        PenarikanDana::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
        ]);

        // Deduct balance immediately or on approval? 
        // Usually best to deduct immediately and put in "escrow" or just pending state.
        $user->decrement('saldo', $request->amount);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification(
                'Permintaan Pencairan Dana',
                "Seller {$user->name} mengajukan penarikan Rp " . number_format($request->amount, 0, ',', '.'),
                'withdrawal'
            ));
        }

        return back()->with('success', 'Permintaan penarikan dana berhasil dikirim.');
    }

    public function adminIndex()
    {
        $withdrawals = PenarikanDana::with('user')->latest()->get();
        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve($id)
    {
        $withdrawal = PenarikanDana::findOrFail($id);
        if ($withdrawal->status !== 'pending') return back();

        $withdrawal->status = 'approved';
        $withdrawal->save();

        // Notify Seller
        $withdrawal->user->notify(new \App\Notifications\SystemNotification(
            'Pencairan Dana Berhasil',
            "Permintaan penarikan dana Rp " . number_format($withdrawal->amount, 0, ',', '.') . " telah disetujui.",
            'withdrawal'
        ));

        return back()->with('success', 'Penarikan dana disetujui.');
    }

    public function reject($id)
    {
        $withdrawal = PenarikanDana::findOrFail($id);
        if ($withdrawal->status !== 'pending') return back();

        $withdrawal->status = 'rejected';
        $withdrawal->save();

        // Refund balance
        $withdrawal->user->increment('saldo', $withdrawal->amount);

        // Notify Seller
        $withdrawal->user->notify(new \App\Notifications\SystemNotification(
            'Pencairan Dana Ditolak',
            "Maaf, penarikan dana Rp " . number_format($withdrawal->amount, 0, ',', '.') . " ditolak admin.",
            'withdrawal'
        ));

        return back()->with('success', 'Penarikan dana ditolak. Saldo dikembalikan ke penjual.');
    }
}

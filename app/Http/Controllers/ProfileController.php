<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Tarik statistik dasar untuk dashboard profil
        $stats = [
            'total_orders' => \App\Models\Order::where('user_id', $user->id)->count(),
            'total_favorites' => \App\Models\Favorite::where('user_id', $user->id)->count(),
        ];

        return view('profile.index', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048' // Batas 2MB
        ]);

        // Handle Upload Foto Profil
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($user->photo && \Storage::disk('public')->exists($user->photo)) {
                \Storage::disk('public')->delete($user->photo);
            }
            
            $path = $request->file('photo')->store('profiles', 'public');
            $validated['photo'] = $path;
        }

        $user->update($validated);
        return back()->with('success', 'Data profil kampus berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => \Hash::make($request->password),
        ]);

        return back()->with('success', 'Status keamanan: Password berhasil diganti.');
    }
}

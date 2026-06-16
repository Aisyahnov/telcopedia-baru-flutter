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

        if ($user->role === 'seller') {
            $stats['total_products'] = \App\Models\Product::where('seller_id', $user->id)->count();
            $stats['seller_orders'] = \App\Models\Order::whereHas('items.product', function($q) use ($user) {
                $q->where('seller_id', $user->id);
            })->count();
        }

        return view('profile.index', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'ktm' => 'nullable|image|max:2048'
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

        // Handle Base64 Foto Profil (dari webcam)
        if ($request->filled('photo_base64')) {
            if ($user->photo && \Storage::disk('public')->exists($user->photo)) {
                \Storage::disk('public')->delete($user->photo);
            }
            
            $image_parts = explode(";base64,", $request->photo_base64);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'profiles/' . uniqid() . '.png';
            
            \Storage::disk('public')->put($fileName, $image_base64);
            $validated['photo'] = $fileName;
        }

        // Handle Upload KTM
        if ($request->hasFile('ktm')) {
            if ($user->ktm && \Storage::disk('public')->exists($user->ktm)) {
                \Storage::disk('public')->delete($user->ktm);
            }
            
            $path = $request->file('ktm')->store('ktm', 'public');
            $validated['ktm'] = $path;
        }

        $user->update($validated);
        return back()->with('success', 'Data profil kampus berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|confirmed',
        ]);

        $request->user()->update([
            'password' => \Hash::make($request->password),
        ]);

        return back()->with('success', 'Status keamanan: Password berhasil diganti.');
    }
    public function verify(Request $request)
    {
        $user = $request->user();
        
        if (!$user->photo || !$user->ktm) {
            return response()->json(['success' => false, 'message' => 'Lengkapi foto profil dan upload KTM terlebih dahulu.'], 400);
        }

        $user->update(['is_verified' => true]);
        
        return response()->json(['success' => true, 'message' => 'Identitas Anda telah berhasil diverifikasi!']);
    }
}

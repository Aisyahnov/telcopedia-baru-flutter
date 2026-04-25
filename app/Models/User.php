<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nim', 'name', 'email', 'password', 'role', 'is_verified', 'phone', 'address', 'photo', 'ktm', 'balance'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function getPenaltyPointsAttribute()
    {
        return \App\Models\ProductReturn::where('status', 'approved')
            ->whereHas('product', function($query) {
                $query->where('seller_id', $this->id);
            })->count();
    }

    public function getIsBannedFromPostingAttribute()
    {
        return $this->penalty_points >= 3;
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

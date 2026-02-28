<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser; // <-- 1. Tambahkan ini
use Filament\Panel; // <-- 2. Tambahkan ini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 3. Tambahkan "implements FilamentUser" di sini
class User extends Authenticatable implements FilamentUser 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    // 4. Tambahkan method ini di bagian paling bawah
    public function canAccessPanel(Panel $panel): bool
    {
        // Berikan akses ke semua user untuk saat ini agar kamu bisa masuk (Testing)
        return true; 
        
        // Catatan: Nanti kalau sistem inventory UD Hidayah Jaya Sakti ini sudah mau dipakai beneran, 
        // ganti "return true;" di atas dengan validasi email admin, contohnya:
        // return str_ends_with($this->email, '@hidayahjayasakti.com');
    }
}
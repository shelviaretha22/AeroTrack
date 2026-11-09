<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Services\NotifikasiService;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
        'avatar_path',
        'theme_preference',
        'email_notifications',
        'notification_preferences',
        'last_login_at',
        'last_login_ip',
        'previous_login_at',
        'previous_login_ip',
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
            'notification_preferences' => 'array',
            'last_login_at' => 'datetime',
            'previous_login_at' => 'datetime',
        ];
    }

    
    /**
     * Membaca IP login dengan aman.
     * Jika data lama gagal didekripsi, kembalikan null.
     */
    protected function lastLoginIp(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if ($value === null) {
                    return null;
                }

                try {
                    return \Illuminate\Support\Facades\Crypt::decryptString($value);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    return null;
                }
            },
            set: fn ($value) => $value === null
                ? null
                : \Illuminate\Support\Facades\Crypt::encryptString($value),
        );
    }

    protected function previousLoginIp(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if ($value === null) {
                    return null;
                }

                try {
                    return \Illuminate\Support\Facades\Crypt::decryptString($value);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    return null;
                }
            },
            set: fn ($value) => $value === null
                ? null
                : \Illuminate\Support\Facades\Crypt::encryptString($value),
        );
    }

    /** Sementara semua akun berperan Commercial (belum ada kolom role). */
    public function getPeranAttribute(): string
    {
        return 'Commercial';
    }

    /** Inisial untuk avatar placeholder. */
    public function getInisialAttribute(): string
    {
        return strtoupper(mb_substr(trim($this->name) ?: '?', 0, 1));
    }

    /** [jenis => bool]. Jenis yang belum pernah diatur dianggap aktif. */
    public function preferensiNotifikasi(): array
    {
        $tersimpan = $this->notification_preferences ?? [];

        return collect(array_keys(NotifikasiService::JENIS))
            ->mapWithKeys(fn ($jenis) => [$jenis => (bool) ($tersimpan[$jenis] ?? true)])
            ->all();
    }
}

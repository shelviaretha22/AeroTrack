<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifikasiDibaca extends Model
{
    protected $table = 'notifikasi_dibaca';

    public $timestamps = false;

    protected $fillable = ['user_id', 'kunci', 'dibaca_at'];

    protected $casts = ['dibaca_at' => 'datetime'];
}

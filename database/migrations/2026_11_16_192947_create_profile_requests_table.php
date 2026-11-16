<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'new_name',
        'new_username',
        'new_email',
        'status',
    ];

    // Relasi ke User pemilik pengajuan
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
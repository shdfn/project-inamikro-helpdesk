<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'priority',
        'status',
        'category_id',
        'mentor_id',
        'it_support_id',
        'attachment',
        'first_response_at',
        'resolved_at',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============ RELASI ============

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function itSupport()
    {
        return $this->belongsTo(User::class, 'it_support_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at', 'desc');
    }

    // ============ ACCESSOR STATUS ============

    /**
     * Label status yang ramah ditampilkan.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'Baru' => 'Baru — Menunggu IT Support',
            'Diproses' => 'Diproses — Sedang Ditangani',
            'Ditangani IT' => 'Ditangani IT — Belum Dibalas',
            'Dibalas IT' => 'Dibalas IT — Menunggu Mentor',
            'Dibalas Mentor' => 'Dibalas Mentor — Menunggu IT',
            'Selesai' => 'Selesai — Menunggu Konfirmasi Mentor',
            'Ditutup' => 'Ditutup — Dikonfirmasi Mentor',
            default => $this->status,
        };
    }

    /**
     * Warna badge Bootstrap soft.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Baru' => 'badge-soft-danger',
            'Diproses' => 'badge-soft-warning',      // ← fallback status lama
            'Ditangani IT' => 'badge-soft-warning',
            'Dibalas IT' => 'badge-soft-primary',
            'Dibalas Mentor' => 'badge-soft-info',
            'Selesai' => 'badge-soft-success',
            'Ditutup' => 'badge-soft-secondary',
            default => 'badge-soft-secondary',
        };
    }

    /**
     * Ikon Bootstrap untuk status.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'Baru' => 'plus-circle',
            'Diproses' => 'arrow-repeat',            // ← fallback status lama
            'Ditangani IT' => 'arrow-repeat',
            'Dibalas IT' => 'chat-left-text',
            'Dibalas Mentor' => 'chat-left-dots',
            'Selesai' => 'check-circle',
            'Ditutup' => 'check2-all',
            default => 'circle',
        };
    }

    /**
     * Label pendek (untuk tabel yang sempit).
     */
    public function getStatusShortAttribute(): string
    {
        return match ($this->status) {
            'Baru' => 'Baru',
            'Diproses' => 'Diproses',
            'Ditangani IT' => 'Ditangani IT',
            'Dibalas IT' => 'Dibalas IT',
            'Dibalas Mentor' => 'Dibalas Mentor',
            'Selesai' => 'Selesai',
            'Ditutup' => 'Ditutup',
            default => $this->status,
        };
    }
}
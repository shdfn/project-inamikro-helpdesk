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
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }
}
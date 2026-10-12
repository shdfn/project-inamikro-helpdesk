<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'ticket_id', 'sender_id', 'type',
        'title', 'body', 'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
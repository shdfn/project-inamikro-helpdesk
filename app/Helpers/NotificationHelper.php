<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    /**
     * Kirim notifikasi.
     */
    public static function send($userId, $ticketId, $senderId, $type, $title, $body = null)
    {
        // Jangan kirim ke diri sendiri
        if ($userId === $senderId) {
            return;
        }

        Notification::create([
            'user_id' => $userId,
            'ticket_id' => $ticketId,
            'sender_id' => $senderId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'is_read' => false,
        ]);
    }
}
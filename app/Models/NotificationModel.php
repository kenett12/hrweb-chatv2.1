<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    protected $allowedFields    = [
        'user_id', 'title', 'message', 'link', 'is_read', 'created_at'
    ];

    /**
     * Helper method to dispatch a notification to a specific user
     */
    public function sendNotification(int $userId, string $title, string $message, string $link = null)
    {
        $data = [
            'user_id'    => $userId,
            'title'      => $title,
            'message'    => $message,
            'link'       => $link,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->insert($data)) {
            // FIRE WEBSOCKET EVENT FOR REALTIME IN-APP PUSH
            if (function_exists('emit_socket_event')) {
                emit_socket_event('new_notification', [
                    'user_id' => $userId, // Important so frontend knows who rules it
                    'title'   => $title,
                    'message' => $message,
                    'link'    => $link
                ]);
            }
            return true;
        } else {
            log_message('error', 'Failed to save notification: ' . print_r($this->errors(), true));
            return false;
        }
    }

    /**
     * Helper method to broadcast a notification to all users of a specific role
     */
    public function broadcastToRole(string $role, string $title, string $message, string $link = null)
    {
        $db = \Config\Database::connect();
        $users = $db->table('users')->where('role', $role)->get()->getResultArray();
        
        $inserts = [];
        foreach ($users as $user) {
            $inserts[] = [
                'user_id'    => $user['id'],
                'title'      => $title,
                'message'    => $message,
                'link'       => $link,
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($inserts)) {
            $this->insertBatch($inserts);

            if (function_exists('emit_socket_event')) {
                emit_socket_event('new_notification', [
                    'user_id' => null, // Signals a broadcast alert to trigger a DB fetch
                    'title'   => $title,
                    'message' => $message,
                    'link'    => $link
                ]);
            }
        }
    }
}

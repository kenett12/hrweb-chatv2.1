<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * AJAX endpoint to fetch recent unread/read notifications for the logged-in user
     */
    public function fetch()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Unauthorized']);
        }

        // Fetch up to 20 recent notifications
        $notifications = $this->notificationModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->findAll();

        $unreadCount = $this->notificationModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return $this->response->setJSON([
            'status'        => 'success',
            'notifications' => $notifications,
            'unread_count'  => $unreadCount
        ]);
    }

    /**
     * Marks a specific notification as read
     */
    public function markAsRead($id)
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Unauthorized']);
        }

        // Ensure the notification belongs to this user
        $notification = $this->notificationModel->find($id);
        if ($notification && $notification['user_id'] == $userId) {
            $this->notificationModel->update($id, ['is_read' => 1]);
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setJSON(['status' => 'error', 'msg' => 'Not found or unauthorized']);
    }

    /**
     * Marks all notifications for a user as read
     */
    public function markAllAsRead()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Unauthorized']);
        }

        $this->notificationModel
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        return $this->response->setJSON(['status' => 'success']);
    }

    /**
     * Deletes all notifications for a user to clear their list completely
     */
    public function clearAll()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Unauthorized']);
        }

        $this->notificationModel->where('user_id', $userId)->delete();

        return $this->response->setJSON(['status' => 'success']);
    }
}

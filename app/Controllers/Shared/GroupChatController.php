<?php

namespace App\Controllers\Shared;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class GroupChatController extends BaseController
{
    protected $roomModel;
    protected $memberModel;
    protected $messageModel;
    protected $userModel;

    public function __construct()
    {
        $this->roomModel    = new \App\Models\ChatRoomModel();
        $this->memberModel  = new \App\Models\ChatRoomMemberModel();
        $this->messageModel = new \App\Models\ChatRoomMessageModel();
        $this->userModel    = new \App\Models\UserModel();
    }

    /**
     * Helper to get the current authenticated user's ID
     */
    private function getCurrentUserId()
    {
        return session()->get('id') ?? session()->get('user_id');
    }

    /**
     * GET /group-chat
     * Load the main group chat UI. Also fetches all active rooms for the user.
     */
    public function index()
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return redirect()->to('login');

        // Fetch all rooms this user is a member of
        $myMemberships = $this->memberModel->where('user_id', $userId)->findAll();
        $roomIds = array_column($myMemberships, 'room_id');

        $activeRooms = [];
        $pendingRooms = []; // Groups awaiting admin approval
        
        if (!empty($roomIds)) {
            $activeRooms = $this->roomModel->whereIn('id', $roomIds)
                                           ->where('status', 'active')
                                           ->where('approval_status', 'approved')
                                           ->findAll();

            // Fetch pending rooms to notify the TSR that their group is awaiting approval
            $pendingRooms = $this->roomModel->whereIn('id', $roomIds)
                                            ->where('status', 'active')
                                            ->where('approval_status', 'pending')
                                            ->findAll();
        }

        $creatorRecord = $this->userModel->find($userId);
        $isAdmin = in_array($creatorRecord['role'], ['admin', 'superadmin']);
        
        // If Admin, also fetch ALL rooms for the Manager Tab
        $allRoomsForManager = [];
        if ($isAdmin) {
            $db = \Config\Database::connect();
            $builder = $db->table('chat_rooms r');
            $builder->select('r.*, u.full_name as creator_name, u.email as creator_email, 
                             (SELECT COUNT(*) FROM chat_room_members m WHERE m.room_id = r.id) as member_count');
            $builder->join('users u', 'u.id = r.created_by', 'left');

            // --- FILTERS ---
            $search = $this->request->getGet('search');
            $status = $this->request->getGet('status');
            $category = $this->request->getGet('filter_category');
            
            if ($search) {
                $builder->groupStart()
                        ->like('r.name', $search)
                        ->orLike('u.full_name', $search)
                        ->orLike('u.email', $search)
                        ->groupEnd();
            }
            
            if ($status) {
                $builder->where('r.approval_status', $status);
            }
            
            if ($category) {
                $builder->where('r.category', $category);
            }

            $builder->orderBy('r.created_at', 'DESC');
            $allRoomsForManager = $builder->get()->getResultArray();
        }

        // Fetch users to populate the "Create Group" dropdown
        $allClients = $this->userModel->where('id !=', $userId)->where('status', 'active')->where('role', 'client')->findAll();
        $allStaff = $this->userModel->where('id !=', $userId)->where('status', 'active')->whereIn('role', ['tsr', 'tsr_level_1', 'superadmin', 'admin'])->findAll();

        $allUsers = array_merge($allStaff, $allClients);

        $this->viewData['activeRooms'] = $activeRooms;
        $this->viewData['pendingRooms'] = $pendingRooms;
        $this->viewData['allRoomsForManager'] = $allRoomsForManager;
        $this->viewData['isAdmin'] = $isAdmin;
        $this->viewData['allUsers'] = $allUsers;
        $this->viewData['currentUserId'] = $userId;
        $this->viewData['title'] = 'Group Chats';

        return view('shared/group_chat/index', $this->viewData);
    }

    /**
     * GET /group-chat/room/(:num)
     * Load history for a specific room. (AJAX endpoint and direct load)
     */
    public function room($roomId)
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);

        // Verify membership
        $isMember = $this->memberModel->where('room_id', $roomId)->where('user_id', $userId)->first();
        if (!$isMember) return $this->response->setJSON(['error' => 'Access Denied'])->setStatusCode(403);

        $room = $this->roomModel->find($roomId);

        // Fetch messages with sender details
        $db = \Config\Database::connect();
        $builder = $db->table('chat_room_messages crm');
        $builder->select('crm.*, u.full_name, u.email, u.role');
        $builder->join('users u', 'u.id = crm.user_id');
        $builder->where('crm.room_id', $roomId);
        $builder->orderBy('crm.created_at', 'ASC');
        $messages = $builder->get()->getResultArray();

        // Calculate fallback display names
        foreach ($messages as &$msg) {
            $msg['display_name'] = !empty($msg['full_name']) ? $msg['full_name'] : explode('@', $msg['email'])[0];
        }

        return $this->response->setJSON([
            'room'     => $room,
            'messages' => $messages
        ]);
    }

    /**
     * POST /group-chat/create
     * Initialize a new group and add initial members
     */
    public function create()
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);

        $groupName = $this->request->getPost('group_name');
        $category  = $this->request->getPost('category') ?? 'general';
        $memberIds = $this->request->getPost('members'); // Array of user IDs
        if (!is_array($memberIds)) {
            $memberIds = [$memberIds];
        }

        if (empty($groupName) || empty($memberIds)) {
            return $this->response->setJSON(['error' => 'Group name and members are required.'])->setStatusCode(400);
        }

        // Determine Approval Status based on creator's role
        $creatorRecord = $this->userModel->find($userId);
        $creatorRole = $creatorRecord['role'];

        $approvalStatus = in_array($creatorRole, ['admin', 'superadmin']) ? 'approved' : 'pending';

        // Set category to general safely if invalid
        if (!in_array($category, ['general', 'confidential'])) {
            $category = 'general';
        }

        // Ensure creator is always in the list
        if (!in_array($userId, $memberIds)) {
            $memberIds[] = $userId;
        }

        // Handle Group Photo Upload
        $roomImage = null;
        $file = $this->request->getFile('group_photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $roomImage = $file->getRandomName();
            $file->move(FCPATH . 'uploads/group_photos', $roomImage);
        }

        // 1. Create Room
        $roomData = [
            'name'            => $groupName,
            'category'        => $category,
            'created_by'      => $userId,
            'status'          => 'active',
            'approval_status' => $approvalStatus,
            'room_image'      => $roomImage,
            'created_at'      => date('Y-m-d H:i:s')
        ];

        if (!$this->roomModel->insert($roomData)) {
            return $this->response->setJSON([
                'error' => $this->roomModel->errors()['name'] ?? 'Failed to create group.'
            ])->setStatusCode(400);
        }

        $roomId = $this->roomModel->getInsertID();

        // 2. Add Members
        $memberBatch = [];
        foreach ($memberIds as $mId) {
            $memberBatch[] = [
                'room_id'   => $roomId,
                'user_id'   => $mId,
                'role'      => ($mId == $userId) ? 'admin' : 'member',
                'joined_at' => date('Y-m-d H:i:s')
            ];
        }
        $this->memberModel->insertBatch($memberBatch);

        // 3. System greeting message
        $creatorName = !empty($creatorRecord['full_name']) ? $creatorRecord['full_name'] : explode('@', $creatorRecord['email'])[0];

        $sysMsg = "{$creatorName} created the group '{$groupName}'.";
        $this->messageModel->insert([
            'room_id'    => $roomId,
            'user_id'    => $userId, // System proxy
            'message'    => $sysMsg,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'room_id' => $roomId,
            'message' => 'Group created successfully.'
        ]);
    }

    /**
     * POST /group-chat/send
     * Endpoint to save a new group message
     */
    public function send()
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);

        $roomId  = $this->request->getPost('room_id');
        $message = $this->request->getPost('message');

        if (empty($roomId) || empty($message)) {
            return $this->response->setJSON(['error' => 'Room ID and message are required.'])->setStatusCode(400);
        }

        // Verify membership
        $isMember = $this->memberModel->where('room_id', $roomId)->where('user_id', $userId)->first();
        if (!$isMember) return $this->response->setJSON(['error' => 'Access Denied'])->setStatusCode(403);

        $msgData = [
            'room_id'    => $roomId,
            'user_id'    => $userId,
            'message'    => $message,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Handle attachment if any (simplified structure)
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/group_chat', $newName);
            $msgData['attachment'] = $newName;
        }

        $msgId = $this->messageModel->insert($msgData);
        $savedMsg = $this->messageModel->find($msgId);

        // Append Sender details for the WebSocket broadcaster to use
        $db = \Config\Database::connect();
        $sender = $db->table('users')->where('id', $userId)->get()->getRowArray();
        $savedMsg['display_name'] = !empty($sender['full_name']) ? $sender['full_name'] : explode('@', $sender['email'])[0];
        $savedMsg['role'] = $sender['role'];

        return $this->response->setJSON([
            'success' => true,
            'message' => $savedMsg
        ]);
    }

    /**
     * POST /group-chat/approve
     * Admin-only endpoint to authorize a pending group chat.
     */
    public function approve()
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);

        $creatorRecord = $this->userModel->find($userId);
        if (!in_array($creatorRecord['role'], ['admin', 'superadmin'])) {
            return $this->response->setJSON(['error' => 'Permission Denied'])->setStatusCode(403);
        }

        $roomId = $this->request->getPost('room_id');
        $this->roomModel->update($roomId, ['approval_status' => 'approved']);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /group-chat/reject
     * Admin-only endpoint to delete a pending group chat request.
     */
    public function reject()
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);

        $creatorRecord = $this->userModel->find($userId);
        if (!in_array($creatorRecord['role'], ['admin', 'superadmin'])) {
            return $this->response->setJSON(['error' => 'Permission Denied'])->setStatusCode(403);
        }

        $roomId = $this->request->getPost('room_id');
        $this->roomModel->delete($roomId);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * POST /group-chat/delete
     * Admin-only endpoint to permanently delete an active group and its assets.
     */
    public function delete()
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);

        $creatorRecord = $this->userModel->find($userId);
        if (!in_array($creatorRecord['role'], ['admin', 'superadmin'])) {
            return $this->response->setJSON(['error' => 'Permission Denied'])->setStatusCode(403);
        }

        $roomId = $this->request->getPost('room_id');
        $room = $this->roomModel->find($roomId);

        if ($room) {
            // Delete image if exists
            if (!empty($room['room_image'])) {
                $path = FCPATH . 'uploads/group_photos/' . $room['room_image'];
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $this->roomModel->delete($roomId);
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * GET /group-chat/members/(:num)
     * AJAX endpoint to fetch participants for a room
     */
    public function getMembers($roomId)
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);

        $db = \Config\Database::connect();
        $builder = $db->table('chat_room_members crm');
        $builder->select('crm.*, u.full_name, u.email, u.role');
        $builder->join('users u', 'u.id = crm.user_id');
        $builder->where('crm.room_id', $roomId);
        
        $members = $builder->get()->getResultArray();

        return $this->response->setJSON($members);
    }
}

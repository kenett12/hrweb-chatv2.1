<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\TicketModel;

/**
 * ChatController
 * Manages the real-time-like chat session, bot interactions, and the reinforcement learning loop.
 */
class ChatController extends BaseController
{
    protected $ticketModel;

    /**
     * Constructor to initialize the Ticket Model.
     */
    public function __construct()
    {
        $this->ticketModel = new TicketModel(); 
    }

    /**
     * 0. Display the Chat Directory (List of all chats)
     */
    public function directory()
    {
        $userId = session()->get('id') ?? session()->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        $companyId = $user['client_id'];
        $companyUsers = $companyId ? $userModel->where('client_id', $companyId)->findColumn('id') : [$userId];

        // Fetch all tickets/chats for this client, ordered by latest update
        $chats = $this->ticketModel
            ->select('tickets.*, COALESCE(t.full_name, s.email) as staff_name')
            ->join('users s', 's.id = tickets.assigned_to', 'left')
            ->join('tsrs t', 't.user_id = s.id', 'left')
            ->whereIn('tickets.client_id', $companyUsers)
            ->orderBy('updated_at', 'DESC')
            ->findAll();

        return view('client/chat_directory', array_merge($this->viewData, [
            'chats'     => $chats,
            'userRole'  => session()->get('role'),
            'userEmail' => session()->get('email')
        ]));
    }

    /**
     * 1. Display the chat session and load history
     */
    public function index($id = null)
    {
        $db = \Config\Database::connect();
        $userId = session()->get('id') ?? session()->get('user_id');

        if (!$userId) return redirect()->to('/login');

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        $companyId = $user['client_id'];
        $companyUsers = $companyId ? $userModel->where('client_id', $companyId)->findColumn('id') : [$userId];

        // Fetch the active ticket
        $activeTicket = $id ? $this->ticketModel->find($id) : $db->table('tickets')
            ->whereIn('client_id', $companyUsers)
            ->where('status', 'Open')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        // If trying to access chat without an active ticket, redirect to directory
        if (!$activeTicket && !$id) {
            return redirect()->to('/client/chat');
        }

        // Fetch the conversation history (this automatically parses bot messages and joins user roles)
        $history = $activeTicket ? $this->ticketModel->getReplies($activeTicket['id']) : [];

        // Fetch quick tips for the sidebar
        $quick_tips = $db->table('kb_articles')
            ->limit(10)
            ->get()
            ->getResultArray();

        return view('client/chat_session', array_merge($this->viewData, [
            'history'       => $history,
            'quick_tips'    => $quick_tips, 
            'active_ticket' => $activeTicket,
            'userRole'      => session()->get('role'),
            'userEmail'     => session()->get('email')
        ]));
    }

    /**
     * 2. Process bot messages via AJAX
     */
    public function handleBotQuery($ticketId)
    {
        try {
            $db = \Config\Database::connect();
            $userId = session()->get('id') ?? session()->get('user_id');
            $rawMsg = $this->request->getPost('message');
            $userMsg = strtolower(trim($rawMsg ?? ''));
            $isQuickQuery = $this->request->getPost('is_quick_query') == '1';

            if (empty($userMsg)) {
                return $this->response->setJSON(['status' => 'error', 'reply' => 'Empty message.']);
            }

            // If ticket is already assigned, mute the bot and route as human message unless explicitly clicked quick tip
            $ticket = $this->ticketModel->find($ticketId);
            $isAssigned = !empty($ticket['assigned_to']);

            if ($isAssigned && !$isQuickQuery) {
                $db->table('ticket_replies')->insert([
                    'ticket_id'  => $ticketId,
                    'user_id'    => $userId,
                    'message'    => esc($rawMsg),
                    'is_bot'     => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                $notifModel = new \App\Models\NotificationModel();
                if (!empty($ticket['assigned_to'])) {
                    $notifModel->sendNotification($ticket['assigned_to'], "New Client Reply", "The client has replied to ticket #{$ticketId}.", base_url("tsr/tickets/view/{$ticketId}"));
                }
                if (!empty($ticket['superadmin_id'])) {
                    $notifModel->sendNotification($ticket['superadmin_id'], "New Client Reply", "The client has replied to ticket #{$ticketId}.", base_url("superadmin/tickets/view/{$ticketId}"));
                }
                
                if (function_exists('emit_socket_event')) {
                    emit_socket_event('new_ticket_message', [
                        'ticket_id'   => $ticketId,
                        'message'     => esc($rawMsg),
                        'is_bot'      => 0,
                        'sender_id'   => $userId,
                        'sender_name' => session()->get('username') ?? session()->get('email') ?? 'User',
                        'time'        => date('h:i A')
                    ]);
                }
                return $this->response->setJSON(['status' => 'success', 'bypassed_bot' => true]);
            }

            $articles = $db->table('kb_articles')->get()->getResultArray();
            $bestMatch = null;
            $maxScore = 0;

            foreach ($articles as $art) {
                $score = $this->calculateWeight($userMsg, $art);
                if ($score > $maxScore) { 
                    $maxScore = $score; 
                    $bestMatch = $art; 
                }
            }

            if ($bestMatch && $maxScore >= 40) {
                // Attach ARTICLE_ID so feedback buttons can map back to the KB entry
                $rawAnswerToSave = "[ARTICLE_ID:" . $bestMatch['id'] . "]\n" . $bestMatch['answer'];
                $replyToReturn = $this->renderBotContent($rawAnswerToSave);
            } else {
                // === SMART AI RAG INTEGRATION ===
                helper('rag'); // Load the smart helper
                $contextChunks = searchContextSync($userMsg, 10);
                
                // Log unanswered queries for manual KB improvement since there was no exact match or context
                if (empty($contextChunks)) {
                    $db->table('bot_unanswered_queries')->insert([
                        'client_id'  => $userId,
                        'user_query' => esc($rawMsg), 
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // Call Ollama regardless of context so it can handle greetings and general conversation
                $rawAnswerToSave = callOllamaSync($userMsg, $contextChunks);
                $replyToReturn = nl2br(parseBasicMarkdown($rawAnswerToSave));
            }

            $this->saveToThread($ticketId, $userId, $rawMsg, $rawAnswerToSave);

            // ── ACTUAL REAL-TIME WEBSOCKET PUSH ──
            if (function_exists('emit_socket_event')) {
                // Emit User's message so TSRs/Admins can see it live
                emit_socket_event('new_ticket_message', [
                    'ticket_id'   => $ticketId,
                    'message'     => esc($rawMsg),
                    'is_bot'      => 0,
                    'sender_id'   => $userId,
                    'sender_name' => session()->get('username') ?? session()->get('email') ?? 'User',
                    'sender_role' => session()->get('role'),
                    'time'        => date('h:i A')
                ]);

                // Emit Bot's message so TSRs/Admins can see it live
                emit_socket_event('new_ticket_message', [
                    'ticket_id'   => $ticketId,
                    'message'     => $replyToReturn,
                    'is_bot'      => 1,
                    'sender_id'   => null,
                    'sender_name' => 'HRWeb Bot',
                    'article_id'  => $bestMatch ? $bestMatch['id'] : null,
                    'time'        => date('h:i A')
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success', 
                'reply' => $replyToReturn,
                'time' => date('h:i A')
            ]);
        } catch (\Throwable $e) {
            log_message('critical', $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    /**
     * 3. Submit Thumbs Up / Thumbs Down Feedback
     * REINFORCEMENT LEARNING: If helpful, the user's specific query is added to article keywords.
     */
    public function submitFeedback()
    {
        try {
            $db = \Config\Database::connect();
            $userId = session()->get('id') ?? session()->get('user_id');
            
            $articleId = $this->request->getPost('article_id');
            $isHelpful = $this->request->getPost('is_helpful'); 
            $userQuery = $this->request->getPost('user_query'); 

            if (!$userId || !$articleId) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'Missing ID data']);
            }

            // 1. Record the feedback in the metrics table
            $data = [
                'article_id' => $articleId,
                'user_id'    => $userId,
                'is_helpful' => $isHelpful,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $db->table('kb_feedback')->insert($data);

            // 2. THE LEARNING LOGIC: If helpful, add the original query to article keywords
            if ($isHelpful == 1 && !empty($userQuery)) {
                $article = $db->table('kb_articles')->where('id', $articleId)->get()->getRowArray();
                
                if ($article) {
                    $currentKeywords = $article['keywords'] ?? '';
                    $newKeyword = strtolower(trim($userQuery));

                    // Append the new keyword if it doesn't already exist
                    if (strpos(strtolower($currentKeywords), $newKeyword) === false) {
                        $updatedKeywords = empty($currentKeywords) ? $newKeyword : $currentKeywords . ', ' . $newKeyword;
                        
                        $db->table('kb_articles')
                           ->where('id', $articleId)
                           ->update(['keywords' => $updatedKeywords]);
                    }
                }
            }

            return $this->response->setJSON(['status' => 'success']);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 4. Manual Chat Send (For talking to Humans/TSR)
     */
    public function send($ticketId)
    {
        try {
            $db = \Config\Database::connect();
            $userId = session()->get('id') ?? session()->get('user_id');
            $message = $this->request->getPost('message');

            if (empty($message)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'Empty message']);
            }

            $db->table('ticket_replies')->insert([
                'ticket_id'  => $ticketId,
                'user_id'    => $userId,
                'message'    => esc($message),
                'is_bot'     => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // ── NOTIFICATIONS ──
            $notifModel = new \App\Models\NotificationModel();
            $ticketInfo = $this->ticketModel->find($ticketId);

            // Notify TSR (if assigned)
            if (!empty($ticketInfo['assigned_to'])) {
                $notifModel->sendNotification(
                    $ticketInfo['assigned_to'], 
                    "New Client Reply", 
                    "The client has replied to ticket #{$ticketId}.", 
                    base_url("tsr/tickets/view/{$ticketId}")
                );
            }

            // Notify Superadmin (if active on the thread)
            if (!empty($ticketInfo['superadmin_id'])) {
                $notifModel->sendNotification(
                    $ticketInfo['superadmin_id'], 
                    "New Client Reply", 
                    "The client has replied to ticket #{$ticketId}.", 
                    base_url("superadmin/tickets/view/{$ticketId}")
                );
            }

            // ── ACTUAL REAL-TIME WEBSOCKET PUSH ──
            if (function_exists('emit_socket_event')) {
                emit_socket_event('new_ticket_message', [
                    'ticket_id'   => $ticketId,
                    'message'     => esc($message),
                    'is_bot'      => 0,
                    'sender_id'   => $userId,
                    'sender_name' => session()->get('username') ?? session()->get('email') ?? 'User',
                    'sender_role' => session()->get('role'),
                    'time'        => date('h:i A')
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'time' => date('h:i A')
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    /**
     * 5. Manual KB Search for the Sidebar
     */
    public function searchKB()
    {
        $db = \Config\Database::connect();
        $term = $this->request->getGet('term');
        
        $builder = $db->table('kb_articles')->select('id, question');
        if (!empty($term)) {
            $builder->like('question', $term)->orLike('keywords', $term);
        }
        
        $results = $builder->limit(10)->get()->getResultArray();
        return $this->response->setJSON($results);
    }

    /**
     * 6. SMART MATCHING LOGIC (The Bot Brain)
     */
    private function calculateWeight($userMsg, $art) {
        $score = 0;
        $question = strtolower($art['question'] ?? '');
        $keywords = strtolower($art['keywords'] ?? ''); 
        
        $userWords = array_filter(explode(' ', preg_replace('/[^a-z0-9]+/i', ' ', $userMsg)));
        if (empty($userWords)) return 0;

        if ($userMsg === $question) return 100;

        $tags = explode(',', $keywords);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (empty($tag)) continue;

            $tagClean = str_replace('_', ' ', $tag);

            if (strpos($userMsg, $tagClean) !== false || strpos($userMsg, $tag) !== false) {
                $score += 90;
                continue; 
            }

            $tagWords = explode(' ', $tagClean);
            foreach ($userWords as $uWord) {
                if (strlen($uWord) > 2) {
                    if (in_array($uWord, $tagWords)) {
                        $score += 50; 
                    }
                }
            }
        }

        if (strpos($question, $userMsg) !== false) {
            $score += 60; 
        } else {
            foreach ($userWords as $uWord) {
                if (strlen($uWord) > 2 && strpos($question, $uWord) !== false) {
                    $score += 20; 
                }
            }
        }

        return $score;
    }

    /**
     * 7. HTML PARSER: Handles Images and dynamic feedback buttons
     */
    private function renderBotContent($text) {
        $text = html_entity_decode($text ?? '');
        $text = str_replace(['<br>', '<br />', '<br/>'], "\n", $text);

        // EXTRACTION: Locate hidden ARTICLE_ID tag
        $articleId = null;
        if (preg_match('/\[ARTICLE_ID:(\d+)\]/i', $text, $matches)) {
            $articleId = $matches[1];
            $text = str_replace($matches[0], '', $text);
        }

        $rendered = preg_replace_callback('/\[(?:IMAGE|IMG_FILE):([^\]]+)\]/i', function($matches) {
            $fileName = trim($matches[1]);
            if (is_numeric($fileName)) return ''; 

            $url = base_url('assets/img/kb/' . $fileName);
            return '<span class="block my-3"><img src="'.$url.'" class="rounded-2xl border border-slate-200 shadow-sm" style="max-width: 100%; height: auto;"></span>';
        }, $text);
        
        $finalHtml = nl2br(trim($rendered));

        // GENERATE FEEDBACK BUTTONS
        if ($articleId) {
            $finalHtml .= '
            <div class="feedback-container mt-3 flex items-center gap-2 border-t border-slate-200/60 pt-2.5 pb-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Was this helpful?</span>
                <button onclick="sendBotFeedback('.$articleId.', 1, this)" class="w-6 h-6 rounded-full bg-white border border-slate-200 text-slate-400 hover:bg-emerald-50 hover:text-emerald-500 hover:border-emerald-200 transition-colors flex items-center justify-center shadow-sm" title="Yes"><i class="fas fa-thumbs-up text-[10px]"></i></button>
                <button onclick="sendBotFeedback('.$articleId.', 0, this)" class="w-6 h-6 rounded-full bg-white border border-slate-200 text-slate-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-colors flex items-center justify-center shadow-sm" title="No"><i class="fas fa-thumbs-down text-[10px]"></i></button>
            </div>';
        }

        return $finalHtml;
    }

    /**
     * 8. Save the thread messages to the database
     */
    private function saveToThread($ticketId, $userId, $userMsg, $botMsg)
    {
        $db = \Config\Database::connect();
        
        $db->table('ticket_replies')->insert([
            'ticket_id'  => $ticketId, 
            'user_id'    => $userId, 
            'message'    => esc($userMsg), 
            'is_bot'     => 0, 
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $db->table('ticket_replies')->insert([
            'ticket_id'  => $ticketId, 
            'user_id'    => null, 
            'message'    => $botMsg, 
            'is_bot'     => 1, 
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 9. Request a Human Agent
     * Clients can click a button to explicitly request human support.
     */
    public function requestAgent($ticketId)
    {
        try {
            $db = \Config\Database::connect();
            $userId = session()->get('id') ?? session()->get('user_id');

            $ticket = $this->ticketModel->find($ticketId);
            if (!$ticket) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'Ticket not found']);
            }

            // Verify they aren't already assigned
            if (!empty($ticket['assigned_to'])) {
                return $this->response->setJSON(['status' => 'success', 'msg' => 'An automated message was sent.']);
            }

            $message = "I would like to speak to a human agent, please.";

            // Insert as a user message
            $db->table('ticket_replies')->insert([
                'ticket_id'  => $ticketId,
                'user_id'    => $userId,
                'message'    => esc($message),
                'is_bot'     => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // ── HR ROUTING & ESCALATION MATRIX ──
            $assignedTo = null;
            $status = 'Open';
            
            // 1. Find company lead TSR
            $userRecord = $db->table('users')->where('id', $userId)->get()->getRowArray();
            $companyId = $userRecord['client_id'] ?? null;
            
            if ($companyId) {
                $clientRecord = $db->table('clients')->where('id', $companyId)->get()->getRowArray();
                if ($clientRecord && !empty($clientRecord['hr_contact'])) {
                    $hrContact = json_decode($clientRecord['hr_contact'], true);
                    $leadTsrId = $hrContact['lead'] ?? null;
                    
                    if ($leadTsrId) {
                        $leadUser = $db->table('users')->where('id', $leadTsrId)->get()->getRowArray();
                        if ($leadUser && $leadUser['availability_status'] === 'active') {
                            $assignedTo = $leadTsrId;
                            $db->table('users')->where('id', $leadTsrId)->update(['availability_status' => 'busy']);
                        } else {
                            // Escalate if Lead is busy/offline
                            $escalationHierarchy = ['tsr_level_1', 'tl', 'supervisor', 'manager', 'dev', 'tsr_level_2', 'it'];
                            foreach ($escalationHierarchy as $role) {
                                $availableStaff = $db->table('users')
                                    ->where('role', $role)
                                    ->where('status', 'active')
                                    ->where('availability_status', 'active')
                                    ->orderBy('id', 'ASC') // Assign to the first available for simplicity
                                    ->get()->getRowArray();
                                    
                                if ($availableStaff) {
                                    $assignedTo = $availableStaff['id'];
                                    $db->table('users')->where('id', $availableStaff['id'])->update(['availability_status' => 'busy']);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            
            // Apply Assignment
            if ($assignedTo) {
                $status = 'In Progress';
                $db->table('tickets')->where('id', $ticketId)->update([
                    'assigned_to' => $assignedTo,
                    'status'      => $status,
                    'updated_at'  => date('Y-m-d H:i:s')
                ]);
            }

            // Notify Assigned TSR or Superadmin if escalation failed
            $notifModel = new \App\Models\NotificationModel();
            
            if ($assignedTo) {
                $notifModel->sendNotification(
                    $assignedTo, 
                    "Agent Requested", 
                    "You have been assigned to Ticket #{$ticketId} after a client requested an agent.", 
                    base_url("tsr/tickets/view/{$ticketId}")
                );
            } else {
                // Fallback: Notify superadmin if nobody is available
                $superadmin = (new \App\Models\UserModel())->where('role', 'superadmin')->first();
                if ($superadmin) {
                    $notifModel->sendNotification(
                        $superadmin['id'], 
                        "Agent Requested", 
                        "Client requested an agent on ticket #{$ticketId}, but no staff are available.", 
                        base_url("superadmin/tickets/view/{$ticketId}")
                    );
                }
            }

            // Real-time Push
            if (function_exists('emit_socket_event')) {
                emit_socket_event('new_ticket_message', [
                    'ticket_id'   => $ticketId,
                    'message'     => esc($message),
                    'is_bot'      => 0,
                    'sender_id'   => $userId,
                    'sender_name' => session()->get('username') ?? session()->get('email') ?? 'User',
                    'sender_role' => session()->get('role'),
                    'time'        => date('h:i A')
                ]);
            }

            return $this->response->setJSON(['status' => 'success', 'time' => date('h:i A')]);
        } catch (\Throwable $e) {
            log_message('critical', $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}
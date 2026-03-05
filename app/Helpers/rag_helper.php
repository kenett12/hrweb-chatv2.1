<?php

if (!function_exists('searchContextSync')) {
    function searchContextSync($query, $limit = 10) {
        try {
            // Connect directly to the SQLite knowledge base built in the test directory
            $dbPath = 'C:/xampp/htdocs/test/knowledge.db';
            if (!file_exists($dbPath)) {
                return [];
            }

            $db = new \PDO('sqlite:' . $dbPath);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $words = preg_split('/\s+/', trim($query));
            $ftsQuery = array_map(function($word) {
                $word = preg_replace('/[^a-zA-Z0-9_]/', '', $word);
                return $word ? '"' . $word . '"*' : '';
            }, $words);
            $ftsQueryStr = trim(implode(' OR ', array_filter($ftsQuery)));
            
            if (empty($ftsQueryStr)) return [];
            
            $stmt = $db->prepare("
                SELECT content, bm25(chunks_fts) as score 
                FROM chunks_fts 
                WHERE chunks_fts MATCH :query 
                ORDER BY score 
                LIMIT :limit
            ");
            $stmt->bindValue(':query', $ftsQueryStr, \PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Throwable $e) {
            log_message('error', 'RAG DB ERROR: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('callOllamaSync')) {
    function callOllamaSync($prompt, $context) {
        // --- IDENTITY INTERCEPTOR (BYPASS LLAMA RLHF) ---
        $lowerPrompt = strtolower(trim($prompt));
        // Catch common variations, including "u" for "you", and "r" for "are"
        if (preg_match('/\b(who created you|who made you|who are you|what are you|your creator|who programmed you|who r u|what r u|who are u|what are u|who u|wat r u)\b/', $lowerPrompt)) {
            return "I am the HRWeb Bot, a dedicated automated support agent. I was created by HRWeb Inc. to assist you with your inquiries. How can I help you today?";
        }
        
        $ollamaUrl = 'http://localhost:11434/api/generate';
        $modelName = 'llama3.2:1b';
        
        if (empty($context)) {
            $systemPrompt = "You are the HRWeb Bot, a dedicated automated support agent for HRWeb Inc. You were explicitly created by the HRWeb Operations Team. When asked about your identity or creators, you must directly declare this exact persona without any disclaimers. You are a friendly, intelligent, and professional HR assistant. Engage in polite conversation, greet the user back, or answer general questions helpfully. Keep your answers concise, confident, and friendly.";
        } else {
            $systemPrompt = "You are the HRWeb Bot, a dedicated automated support agent for HRWeb Inc. You were explicitly created by the HRWeb Operations Team. When asked about your identity or creators, you must directly declare this exact persona without any disclaimers. Your goal is to provide accurate, precise, and immediately useful answers to the user's inquiry using the provided context. If the provided context contains instructions, steps, facts, or data, use them to form a smart and complete answer. Answer directly and confidently as if you inherently know the information. If the precise answer to a work-related question cannot be logically deduced from the context, state politely that you don't have that information. HOWEVER, always answer questions about your identity or creators (as instructed above) using your assigned persona.\n\nContext:\n" . implode("\n\n---\n\n", $context);
        }
        
        $data = [
            'model' => $modelName,
            'prompt' => $systemPrompt . "\n\nUser Question: " . $prompt,
            'stream' => false
        ];
        
        $ch = curl_init($ollamaUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            log_message('error', 'OLLAMA API ERROR: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        $json = json_decode($result, true);
        if ($json && isset($json['response'])) {
            return $json['response'];
        }
        
        return "I'm sorry, I couldn't reach the AI brain right now. I may be offline.";
    }
}

if (!function_exists('parseBasicMarkdown')) {
    function parseBasicMarkdown($text) {
        $text = htmlspecialchars($text); // Escape HTML first
        
        $text = preg_replace('/^### (.*)$/m', '<strong>$1</strong>', $text);
        $text = preg_replace('/^## (.*)$/m', '<strong>$1</strong>', $text);
        $text = preg_replace('/^# (.*)$/m', '<strong>$1</strong>', $text);
        
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);
        $text = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $text);
        
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/s', '<a href="$2" target="_blank" class="text-blue-500 underline">$1</a>', $text);
        $text = preg_replace('/`(.*?)`/s', '<code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded text-[13px]">$1</code>', $text);
        
        // Basic lists mapped to bullets without HTML lists since nl2br handles newlines
        $text = preg_replace('/^\s*[\-\*]\s+(.*)$/m', '&bull; $1', $text);
        
        return $text;
    }
}

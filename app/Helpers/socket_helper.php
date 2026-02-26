<?php

if (!function_exists('emit_socket_event')) {
    /**
     * Bridges CodeIgniter PHP Backend to Node.js Socket.IO Server.
     * Making a lightweight local cURL POST request to trigger real-time push events.
     *
     * @param string $event The name of the socket event (e.g., 'new_notification', 'ticket_update')
     * @param array $data The payload to broadcast
     * @return bool True if successful, False otherwise
     */
    function emit_socket_event($event, $data = [])
    {
        $url = 'http://127.0.0.1:3001/emit-message';
        $payload = json_encode([
            'event' => $event,
            'data'  => $data
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ]);
        
        // Timeout is fast so PHP isn't blocked if node is down
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}

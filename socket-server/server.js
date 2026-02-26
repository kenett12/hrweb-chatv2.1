/**
 * HRWeb Unified Socket Server
 * Handles: Real-time Tickets, Direct Messaging, WebRTC Calls, and Typing Indicators.
 */

process.env.TZ = "Asia/Manila"; // Ensure timezone consistency

import express from "express";
import { createServer } from "http";
import { Server } from "socket.io";
import cors from "cors";
import mysql from "mysql2/promise"; 

const app = express();
app.use(cors());
app.use(express.json());

// ── 1. Database Connection (XAMPP/MySQL) ──────────────────
const dbConfig = {
    host: "localhost",
    user: "root",
    password: "", 
    database: "chat-app" 
};

let pool;
try {
    pool = mysql.createPool({ 
        ...dbConfig, 
        waitForConnections: true, 
        connectionLimit: 10,
        queueLimit: 0 
    });
    console.log("🗄️ MySQL Pool Created");
} catch (err) { 
    console.error("❌ MySQL Pool Error:", err); 
}

const server = createServer(app);
const io = new Server(server, {
    cors: { 
        origin: "*", 
        methods: ["GET", "POST"], 
        credentials: true 
    },
    path: "/socket.io",
    transports: ["websocket", "polling"],
});

// ── 2. PHP API Bridge (For CodeIgniter Triggers) ───────────
// This allows your PHP controllers to push updates to the socket server
app.post('/emit-message', (req, res) => {
    const { event, data } = req.body;
    if (!event || !data) return res.status(400).json({ error: 'Missing event/data' });

    console.log(`📩 PHP Event Received: ${event}`);

    switch (event) {
        case 'new_ticket_message':
            // Broadcast to the specific ticket room (e.g., ticket_15)
            io.to(`ticket_${data.ticket_id}`).emit('new_ticket_message', data);
            break;

        case 'new_message':
            // Existing direct messaging logic
            const roomName = [String(data.sender_id), String(data.receiver_id)].sort().join('-');
            io.to(roomName).emit("receive_message", data);
            break;
            
        default:
            io.emit(event, data);
            break;
    }
    res.json({ success: true });
});

// ── 3. Real-Time Socket Events ────────────────────────────
const onlineUsers = new Map();

io.on("connection", (socket) => {
    console.log(`🔌 New Connection: ${socket.id}`);

    // A. User Lifecycle Management
    socket.on("user_connected", (userId) => {
        if (!userId) return;
        const userIdStr = String(userId);
        socket.join(`user_${userIdStr}`);
        socket.userId = userIdStr;
        onlineUsers.set(userIdStr, socket.id);
        
        io.emit("user_status_change", { user_id: userId, status: 'online' });
    });

    // B. Unified Ticket System Logic
    socket.on("join_ticket", (ticketId) => {
        const room = `ticket_${ticketId}`;
        socket.join(room);
        console.log(`🎫 Socket ${socket.id} joined ticket room: ${room}`);
    });

    socket.on("send_message", (data) => {
        // Broadcasts ticket messages to everyone in the thread
        const room = `ticket_${data.ticket_id}`;
        io.to(room).emit("new_ticket_message", {
            ticket_id: data.ticket_id,
            message: data.message,
            is_bot: data.is_bot || 0,
            sender_id: data.sender_id,
            sender_name: data.username,
            created_at: new Date().toISOString()
        });
    });

    // C. Messenger-Style Typing Indicators
    socket.on("ticket_typing", (data) => {
        // data: { ticket_id, username, is_typing }
        const room = `ticket_${data.ticket_id}`;
        socket.to(room).emit("ticket_typing", data); // Notify others in the room
    });

    // D. Read Receipts (Message Seen)
    socket.on("message_seen", (data) => {
        const room = `ticket_${data.ticket_id}`;
        socket.to(room).emit("message_seen", data);
    });

    // E. Existing WebRTC Call Logic (Keep your features)
    socket.on("call_request", (data) => {
        io.to(`user_${data.target_id}`).emit("call_request", data);
    });

    socket.on("call_accepted", (data) => {
        io.to(`user_${data.target_id}`).emit("call_accepted", data);
    });

    socket.on("ice_candidate", (data) => {
        io.to(`user_${data.target_id}`).emit("ice_candidate", data.candidate);
    });

    // F. Disconnection Handling
    socket.on("disconnect", () => {
        if (socket.userId) {
            onlineUsers.delete(socket.userId);
            io.emit("user_status_change", { user_id: socket.userId, status: 'offline' });
        }
        console.log(`❌ Disconnected: ${socket.id}`);
    });
});

// ── 4. Start Server ───────────────────────────────────────
const PORT = 3001;
server.listen(PORT, () => {
    console.log(`🚀 HRWeb Socket Server running on http://localhost:${PORT}`);
});
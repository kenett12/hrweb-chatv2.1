/**
 * Global Scope Helpers - Fixing the "Not a function" errors
 */
window.clearImage = () => {
    const fileInput = document.getElementById('file-input');
    const previewBox = document.getElementById('img-preview-box');
    if (fileInput) fileInput.value = "";
    if (previewBox) previewBox.classList.add('hidden');
};

window.confirmMatch = async (id, query) => {
    const fd = new FormData();
    fd.append('is_feedback', 'yes');
    fd.append('article_id', id);
    fd.append('message', query);
    fd.append(CSRF_NAME, CSRF_TOKEN);

    try {
        const res = await fetch(`${BASE_URL}/client/chat/handleBotQuery`, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        // Find the last suggestion bubble and replace it with the bot's success message
        const suggestions = document.querySelectorAll('.msg-bot');
        const lastSuggestion = suggestions[suggestions.length - 1];
        if (lastSuggestion) lastSuggestion.innerHTML = data.reply;
    } catch (err) {
        console.error("Feedback error:", err);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    const chatBox = document.getElementById('chat-box');

    chatBox.scrollTop = chatBox.scrollHeight;

    // --- Enter Key Fix ---
    userInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // --- Auto-height Fix ---
    userInput.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // --- Main Send Logic ---
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = userInput.value.trim();
        if (!text) return;

        appendMessage('user', text);
        userInput.value = '';
        userInput.style.height = 'auto';

        const fd = new FormData();
        fd.append('message', text);
        fd.append(CSRF_NAME, CSRF_TOKEN);

        try {
            const res = await fetch(`${BASE_URL}/client/chat/handleBotQuery`, {
                method: 'POST',
                body: fd
            });
            const data = await res.json();

            if (data.status === 'suggest') {
                appendSuggestion(data.reply, data.article_id, text);
            } else {
                setTimeout(() => appendMessage('bot', data.reply), 400);
            }
        } catch (err) {
            console.error("Query error:", err);
            appendMessage('bot', "Sorry, I'm having trouble connecting to the server.");
        }
    });

    function appendMessage(sender, text) {
        const isUser = sender === 'user';
        const wrapper = document.createElement('div');
        wrapper.className = `flex items-start gap-3 mt-6 ${isUser ? 'ml-auto flex-row-reverse' : ''}`;

        wrapper.innerHTML = `
            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-[10px] font-black ${isUser ? 'bg-[#1e72af] text-white' : 'bg-blue-50 text-clr-blue'}">${isUser ? 'YOU' : 'HB'}</div>
            <div class="msg-bubble ${isUser ? 'msg-user' : 'msg-bot'}">${text.replace(/\n/g, '<br>')}</div>`;

        chatBox.appendChild(wrapper);
        chatBox.scrollTo({
            top: chatBox.scrollHeight,
            behavior: 'smooth'
        });
    }

    function appendSuggestion(text, id, originalQuery) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-start gap-3 mt-6';

        // Escape quotes in the query to prevent JS errors
        const safeQuery = originalQuery.replace(/'/g, "\\'");

        wrapper.innerHTML = `
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-clr-blue flex items-center justify-center shrink-0 text-[10px] font-black">HB</div>
            <div class="msg-bubble msg-bot">
                <p>${text}</p>
                <div class="flex gap-2 mt-3">
                    <button onclick="confirmMatch(${id}, '${safeQuery}')" class="bg-[#1e72af] text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-700 transition">Yes</button>
                    <button onclick="this.closest('.msg-bubble').innerHTML='I will keep learning! Connecting you to a human...'" class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold hover:bg-gray-200 transition">No</button>
                </div>
            </div>`;
        chatBox.appendChild(wrapper);
        chatBox.scrollTo({
            top: chatBox.scrollHeight,
            behavior: 'smooth'
        });
    }
});
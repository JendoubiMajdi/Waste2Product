<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LABIB - Your Waste-to-Product Assistant</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #00927E;
            --secondary-color: #00b894;
            --labib-color: #4a90e2;
            --user-color: #00927E;
            --bg-gradient: linear-gradient(135deg, #00927E 0%, #00b894 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .chat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
        }

        .chat-header {
            background: var(--bg-gradient);
            color: white;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chat-header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bot-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .chat-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .chat-header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            background: #f8f9fa;
        }

        .message {
            display: flex;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.labib {
            justify-content: flex-start;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .message.labib .message-avatar {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            margin-right: 12px;
        }

        .message.user .message-avatar {
            background: var(--bg-gradient);
            color: white;
            margin-left: 12px;
        }

        .message-content {
            max-width: 70%;
            padding: 15px 20px;
            border-radius: 18px;
            word-wrap: break-word;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .message.labib .message-content {
            background: white;
            border-bottom-left-radius: 4px;
        }

        .message.user .message-content {
            background: var(--bg-gradient);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 5px;
        }

        .chat-input-container {
            padding: 20px 30px;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .input-wrapper {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        #messageInput {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        #messageInput:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 146, 126, 0.1);
        }

        .btn-send {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--bg-gradient);
            border: none;
            color: white;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .btn-send:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 146, 126, 0.3);
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .typing-indicator {
            display: none;
            padding: 15px 20px;
            background: white;
            border-radius: 18px;
            border-bottom-left-radius: 4px;
            max-width: 80px;
        }

        .typing-indicator span {
            height: 10px;
            width: 10px;
            background: #90949c;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            animation: typing 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }

        .welcome-message {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .welcome-message .bot-icon {
            font-size: 80px;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .welcome-message h2 {
            color: #212529;
            margin-bottom: 10px;
        }

        .btn-clear {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-clear:hover {
            background: white;
            color: var(--primary-color);
        }

        /* Scrollbar Styling */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="bot-avatar">
                    🤖
                </div>
                <div>
                    <h1>LABIB</h1>
                    <p>Your Waste-to-Product Expert Assistant</p>
                </div>
            </div>
            <button class="btn-clear" onclick="clearHistory()">
                <i class="bi bi-trash"></i> Clear Chat
            </button>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages" id="chatMessages">
            @if($conversations->isEmpty())
            <div class="welcome-message">
                <div class="bot-icon">🌱</div>
                <h2>Hello{{ auth()->user() ? ', ' . auth()->user()->name : '' }}!</h2>
                <p>I'm LABIB, your friendly assistant for all things waste-to-product!</p>
                <p>Ask me anything about recycling, upcycling, waste management, or converting waste into valuable products.</p>
            </div>
            @else
                @foreach($conversations as $conversation)
                    <!-- User Message -->
                    <div class="message user">
                        <div class="message-content">
                            <div>{{ $conversation->message }}</div>
                            <div class="message-time">{{ $conversation->created_at->format('h:i A') }}</div>
                        </div>
                        <div class="message-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                        </div>
                    </div>

                    <!-- LABIB Response -->
                    <div class="message labib">
                        <div class="message-avatar">
                            🤖
                        </div>
                        <div class="message-content">
                            <div>{{ $conversation->response }}</div>
                            <div class="message-time">{{ $conversation->created_at->format('h:i A') }}</div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Typing Indicator -->
            <div class="message labib" id="typingIndicator">
                <div class="message-avatar">
                    🤖
                </div>
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="chat-input-container">
            <div class="input-wrapper">
                <input 
                    type="text" 
                    id="messageInput" 
                    placeholder="Ask LABIB about waste recycling, upcycling ideas, or converting waste to products..."
                    maxlength="1000"
                >
                <button class="btn-send" id="sendButton" onclick="sendMessage()">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Auto-scroll to bottom
        function scrollToBottom() {
            const chatMessages = document.getElementById('chatMessages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Send message on Enter key
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Send Message Function
        async function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) return;

            const sendButton = document.getElementById('sendButton');
            const chatMessages = document.getElementById('chatMessages');
            const typingIndicator = document.getElementById('typingIndicator');

            // Disable input
            input.disabled = true;
            sendButton.disabled = true;

            // Add user message to chat
            const userMessageHTML = `
                <div class="message user">
                    <div class="message-content">
                        <div>${escapeHtml(message)}</div>
                        <div class="message-time">${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</div>
                    </div>
                    <div class="message-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                    </div>
                </div>
            `;
            
            chatMessages.insertAdjacentHTML('beforeend', userMessageHTML);
            input.value = '';
            scrollToBottom();

            // Show typing indicator
            typingIndicator.style.display = 'flex';
            document.querySelector('.typing-indicator').style.display = 'block';
            scrollToBottom();

            try {
                // Send to server
                const response = await fetch('/chatbot/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                // Hide typing indicator
                typingIndicator.style.display = 'none';

                if (data.success) {
                    // Add LABIB response
                    const labibMessageHTML = `
                        <div class="message labib">
                            <div class="message-avatar">
                                🤖
                            </div>
                            <div class="message-content">
                                <div>${escapeHtml(data.response)}</div>
                                <div class="message-time">${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</div>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', labibMessageHTML);
                } else {
                    console.error('LABIB Error Details:', data.details);
                    console.error('Full error response:', data);
                    alert(data.error || 'Failed to send message. Please try again.');
                }
            } catch (error) {
                typingIndicator.style.display = 'none';
                alert('Network error. Please check your connection and try again.');
                console.error('Error:', error);
            }

            // Re-enable input
            input.disabled = false;
            sendButton.disabled = false;
            input.focus();
            scrollToBottom();
        }

        // Clear Chat History
        async function clearHistory() {
            if (!confirm('Are you sure you want to clear all chat history?')) {
                return;
            }

            try {
                const response = await fetch('/chatbot/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (response.ok) {
                    location.reload();
                }
            } catch (error) {
                alert('Failed to clear history. Please try again.');
                console.error('Error:', error);
            }
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Initial scroll to bottom
        scrollToBottom();
    </script>
</body>
</html>

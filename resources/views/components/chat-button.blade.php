<!-- Floating Chat Button -->
<div class="floating-chat-button" onclick="window.location.href='{{ route('chatbot') }}'">
    <div class="chat-icon">
        🤖
    </div>
    <div class="chat-tooltip">Chat with LABIB</div>
</div>

<style>
    .floating-chat-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 146, 126, 0.4);
        z-index: 1000;
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }

    .floating-chat-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(0, 146, 126, 0.6);
    }

    .chat-icon {
        font-size: 28px;
        animation: bounce 1s infinite alternate;
    }

    .chat-tooltip {
        position: absolute;
        right: 70px;
        background: white;
        color: #333;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .floating-chat-button:hover .chat-tooltip {
        opacity: 1;
    }

    .chat-tooltip::after {
        content: '';
        position: absolute;
        right: -6px;
        top: 50%;
        transform: translateY(-50%);
        border: 6px solid transparent;
        border-left-color: white;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 4px 20px rgba(0, 146, 126, 0.4);
        }
        50% {
            box-shadow: 0 4px 30px rgba(0, 146, 126, 0.6);
        }
    }

    @keyframes bounce {
        from {
            transform: translateY(0);
        }
        to {
            transform: translateY(-5px);
        }
    }

    @media (max-width: 768px) {
        .floating-chat-button {
            width: 50px;
            height: 50px;
            bottom: 20px;
            right: 20px;
        }

        .chat-icon {
            font-size: 24px;
        }

        .chat-tooltip {
            display: none;
        }
    }
</style>

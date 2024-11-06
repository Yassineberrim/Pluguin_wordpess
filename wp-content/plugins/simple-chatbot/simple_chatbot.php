<?php
/**
 * Plugin Name: Simple Chatbot
 * Plugin URI: http://localhost:8000/simple-chatbot/
 * Description: chatbot plugin for WordPress
 * Version: 1.0
 * Author: Yassine berrim
 * Author URI: localhost:8000
 */

defined('ABSPATH') or die('Hey, you can\'t access this file!');

function chatbot_enqueue_scripts() {
    wp_enqueue_style(
        'simple-chatbot-style',
        plugins_url('css/chatbot.css', __FILE__),
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'simple-chatbot-script',
        plugins_url('js/chatbot.js', __FILE__),
        array('jquery'),
        '1.0.0',
        true
    );
    wp_localize_script(
        'simple-chatbot-script',
        'chatbotAjax',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('chatbot-nonce')
        )
    );
}
add_action('wp_enqueue_scripts', 'chatbot_enqueue_scripts');

function chatbot_add_to_footer() {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chat Interface</title>
        <link rel="stylesheet" href="/css/chatbot.css">
    </head>
    <body>
        <button class="floating-button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
        </button>

        <div class="chat-container">
            <div class="chat-header">Service Client</div>
            <div class="chat-box"></div>
            <div class="chat-input-container">
                <input type="text" class="chat-input" placeholder="Écrivez votre message...">
                <button class="send-button">Envoyer</button>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const floatingButton = document.querySelector('.floating-button');
                const chatContainer = document.querySelector('.chat-container');
                const chatBox = document.querySelector('.chat-box');
                const chatInput = document.querySelector('.chat-input');
                const sendButton = document.querySelector('.send-button');

                // Toggle chat window
                floatingButton.addEventListener('click', function() {
                    this.classList.toggle('active');
                    chatContainer.classList.toggle('active');
                    if (chatContainer.classList.contains('active')) {
                        chatInput.focus();
                    }
                });

                // Send message function
                function sendMessage() {
                    const message = chatInput.value.trim();
                    if (message) {
                        // Add user message
                        addMessage(message, 'user');
                        chatInput.value = '';
                        
                        // Simulate bot response
                        setTimeout(() => {
                            addMessage('Merci pour votre message. Un conseiller vous répondra bientôt.', 'bot');
                        }, 1000);
                    }
                }

                // Add message to chat
                function addMessage(text, sender) {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('chat-message', `${sender}-message`);
                    messageDiv.textContent = text;
                    chatBox.appendChild(messageDiv);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }

                // Send button click
                sendButton.addEventListener('click', sendMessage);

                // Enter key press
                chatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        sendMessage();
                    }
                });

                // Add initial bot message
                setTimeout(() => {
                    addMessage('Bonjour! Comment puis-je vous aider?', 'bot');
                }, 500);
            });
        </script>
    </body>
    </html>
    <?php
}

add_action('wp_footer', 'chatbot_add_to_footer');

add_action('wp_footer', 'chatbot_add_to_footer');

add_action('wp_footer', 'chatbot_add_to_footer');

function chatbot_handle_message() {
    check_ajax_referer('chatbot-nonce', 'nonce');

    $message = sanitize_text_field($_POST['message']);

    // Add more robust input sanitization if needed
    // ...

    $response = "Désolé, je ne comprends pas encore cette requête.";

    // Improved intent detection and response generation
    if (stripos($message, 'bonjour') !== false || stripos($message, 'salut') !== false) {
        $response = "Bonjour ! Comment puis-je vous aider aujourd'hui ?";
    } elseif (stripos($message, 'aide') !== false) {
        $response = "Je peux vous aider avec différentes questions. Que souhaitez-vous savoir ?";
    } else {
        // Consider using a more advanced NLP library here
        // ...
    }

    // Send the response with an appropriate status code
    wp_send_json_success(array(
        'reply' => $response
    ));
}
add_action('wp_ajax_chatbot_message', 'chatbot_handle_message');
add_action('wp_ajax_nopriv_chatbot_message', 'chatbot_handle_message');

function chatbot_activate() {
}
register_activation_hook(__FILE__, 'chatbot_activate');


function chatbot_deactivate() {
}
register_deactivation_hook(__FILE__, 'chatbot_deactivate');
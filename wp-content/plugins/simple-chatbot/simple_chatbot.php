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
        <title>Chatbot</title>
        <link rel="stylesheet" href="<?php echo plugins_url('css/chatbot.css', __FILE__); ?>">
    </head>
    <body>
        <button id="chatToggleButton" class="floating-button" onclick="toggleChat()">💬 Chat</button>
        <div class="chat-container" id="chatContainer">
            <header class="chat-header">
                <h2>Bot Assistant</h2>
                <button class="minimize-btn" onclick="toggleChat()">_</button>
            </header>
            <div class="chat-box" id="chatBox">
                <div class="chat-message bot-message">Bonjour ! Comment puis-je vous aider aujourd'hui ?</div>
            </div>
            <div class="chat-input">
                <input type="text" id="userInput" placeholder="Écrivez un message..." onkeypress="handleKeyPress(event)">
                <button onclick="sendMessage()">Envoyer</button>
            </div>
        </div>

        <script src="<?php echo plugins_url('js/chatbot.js', __FILE__); ?>"></script>
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
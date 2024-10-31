function toggleChat() {
    const chatContainer = document.getElementById('chatContainer');
    if (chatContainer.style.display === 'none' || chatContainer.style.display === '') {
        chatContainer.style.display = 'flex'; 
    } else {
        chatContainer.style.display = 'none';
    }
}

function sendMessage() {
    const userInput = document.getElementById('userInput');
    const chatBox = document.getElementById('chatBox');
    const message = userInput.value.trim();

    if (message === '') return;

    const userMessage = document.createElement('div');
    userMessage.className = 'chat-message user-message';
    userMessage.textContent = message;
    chatBox.appendChild(userMessage);

    userInput.value = '';

    setTimeout(() => {
        const botMessage = document.createElement('div');
        botMessage.className = 'chat-message bot-message';
        botMessage.textContent = generateBotResponse(message);
        chatBox.appendChild(botMessage);

        chatBox.scrollTop = chatBox.scrollHeight;
    }, 1000);
}

function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

function generateBotResponse(message) {
    message = message.toLowerCase();

    if (message.includes("bonjour")) {
        return "cava ?";
    } else if (message.includes("Hamd et toi ?")) {
        return "cava hamd fin chada ? ";
    } else if (message.includes("thala frassek !")) {
        return "Au revoir ! Passez une bonne journée !";
    } else {
        return "Je suis désolé, je n'ai pas compris votre demande.";
    }
}
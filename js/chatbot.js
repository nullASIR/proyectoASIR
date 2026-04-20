document.addEventListener('DOMContentLoaded', () => {
    // 1. Inyectar estilos para el chatbot sin blur
    const style = document.createElement('style');
    style.innerHTML = `
        .nav-chatbot-wrapper { display: none !important; }

        .floating-chat-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 99999;
            font-family: 'Nunito Sans', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .chat-toggle-btn {
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border: 2px solid rgba(59, 130, 246, 0.5);
            border-radius: 50%;
            color: white;
            font-size: 32px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4), 0 0 15px rgba(59, 130, 246, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            outline: none;
            position: relative;
        }

        .chat-toggle-btn:hover {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5), 0 0 25px rgba(59, 130, 246, 0.6);
            border-color: #3b82f6;
        }

        .chat-toggle-btn.open {
            transform: scale(0.9);
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-color: rgba(239, 68, 68, 0.5);
            font-size: 24px;
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4);
        }

        .chat-window {
            position: absolute;
            bottom: 90px;
            right: 0;
            width: 380px;
            height: 520px;
            background: rgba(15, 23, 42, 0.65); /* Fondo semitransparente premium */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-top: 2px solid #3b82f6;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(59, 130, 246, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            transform: translateY(30px) scale(0.95);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-origin: bottom right;
        }

        .chat-window.active {
            opacity: 1;
            pointer-events: all;
            transform: translateY(0) scale(1);
        }

        .chat-header {
            background: rgba(30, 41, 59, 0.5); /* Más transparente */
            padding: 20px 24px; /* Mejor padding */
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            color: #ffffff;
            font-size: 1.2rem;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.5px;
        }

        .chat-header-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
            position: relative;
        }

        .chat-header-avatar::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid #0f172a;
        }

        .chat-history {
            flex: 1;
            padding: 24px; /* padding ajustado */
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: transparent;
        }

        .chat-history::-webkit-scrollbar { width: 5px; }
        .chat-history::-webkit-scrollbar-track { background: transparent; }
        .chat-history::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }

        .chat-bubble {
            max-width: 85%;
            padding: 14px 20px; /* padding ajustado */
            border-radius: 18px;
            font-size: 0.95rem;
            line-height: 1.5;
            animation: bounceIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            word-wrap: break-word;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); /* Mejor sombra para los mensajes */
        }

        @keyframes bounceIn {
            from { opacity: 0; transform: scale(0.9) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .chat-bubble.bot {
            align-self: flex-start;
            background: rgba(30, 41, 59, 0.85); /* Burbuja con opacidad */
            color: #f8fafc;
            border-bottom-left-radius: 4px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .chat-bubble.user {
            align-self: flex-end;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.9)); /* Gradiente con opacidad */
            color: white;
            border-bottom-right-radius: 4px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 5px 2px;
            align-items: center;
            height: 20px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            background: #cbd5e1;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out both;
        }

        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }

        .chat-input-area {
            display: flex;
            padding: 20px 24px; /* Padding más amplio y estético */
            background: rgba(30, 41, 59, 0.5); /* Transparente para sumar al Glassmorphism */
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            gap: 12px;
            align-items: center;
        }

        .chat-input-area input {
            flex: 1;
            padding: 14px 18px;
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s;
        }

        .chat-input-area input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .chat-input-area input:focus {
            background: #1e293b;
            border-color: #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }

        .chat-send-btn {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            border: none;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
        }

        .chat-send-btn:hover {
            transform: scale(1.08) rotate(15deg);
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.6);
        }
        
        .chat-send-btn:active {
            transform: scale(0.95);
        }
    `;
    document.head.appendChild(style);

    const chatbotHTML = `
        <div class="floating-chat-container" id="fcContainer">
            <button class="chat-toggle-btn" id="fcToggleBtn" aria-label="Abrir chat">🤖</button>
            <div class="chat-window" id="fcWindow">
                <div class="chat-header">
                    <div class="chat-header-avatar">⚡</div>
                    <span>NexusBot AI</span>
                </div>
                <div class="chat-history" id="fcHistory">
                    <div class="chat-bubble bot">
                        ¡Hola Entrenador! Soy la Inteligencia Artificial de PokePimas 🧠.<br><br>Estoy conectado para ayudarte con lo que necesites sobre nuestra tienda.
                    </div>
                </div>
                <form class="chat-input-area" id="fcForm">
                    <input type="text" id="fcInput" placeholder="Hazme una pregunta..." autocomplete="off">
                    <button type="submit" class="chat-send-btn">➤</button>
                </form>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', chatbotHTML);

    const toggleBtn = document.getElementById('fcToggleBtn');
    const chatWindow = document.getElementById('fcWindow');
    const chatHistory = document.getElementById('fcHistory');
    const chatForm = document.getElementById('fcForm');
    const chatInput = document.getElementById('fcInput');

    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.toggle('active');
        if (chatWindow.classList.contains('active')) {
            toggleBtn.classList.add('open');
            toggleBtn.innerHTML = '✖';
            setTimeout(() => chatInput.focus(), 300);
        } else {
            toggleBtn.classList.remove('open');
            toggleBtn.innerHTML = '🤖';
        }
    });

    function addMessage(text, sender) {
        const msg = document.createElement('div');
        msg.className = `chat-bubble ${sender}`;
        msg.innerHTML = text;
        chatHistory.appendChild(msg);
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    function showTypingIndicator() {
        const msg = document.createElement('div');
        msg.className = 'chat-bubble bot typing';
        msg.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        msg.id = "typingIndicator";
        chatHistory.appendChild(msg);
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    // ============================================
    // INTEGRACIÓN API GEMINI 2.0 FLASH
    const GEMINI_API_KEY = "AIzaSyAUdeHqIAdT4O48tnVglygfcRikZeDmYv8";
    // ============================================

    async function getGeminiResponse(userText) {
        try {
            const systemPrompt = "Eres PimasBot, el asistente Inteligente TCG de la prestigiosa tienda PokePimas. Eres experto, amable y breve. Das soluciones rápidas de e-commerce (envíos en 24h/48h asegurados, metodos de pago como tarjeta, bizum y paypal seguros, productos 100% verificados y devoluciones en 14 dias para sellados). Usa unos pocos emojis si es necesario, sin pasarte. Responde medianamente breve. Responde la siguiente duda del usuario: ";

            const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${GEMINI_API_KEY}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    contents: [{
                        parts: [{ text: systemPrompt + userText }]
                    }],
                    generationConfig: {
                        temperature: 0.7,
                        maxOutputTokens: 2500
                    }
                })
            });

            if (!response.ok) {
                console.error("Error from API:", response.status, await response.text());
                return "Ay... mis circuitos 🤖. Ha ocurrido un error al conectar con los servidores.";
            }

            const data = await response.json();

            let modelReply = (data.candidates && data.candidates[0].content && data.candidates[0].content.parts[0].text)
                ? data.candidates[0].content.parts[0].text
                : "Uf, mis pensamientos se entrecruzaron. 😵 ¿Podrías repetirlo?";
            modelReply = modelReply.replace(/\n/g, "<br>");
            return modelReply;

        } catch (error) {
            console.error("Fetch error:", error);
            return "Lo siento, tuve un corte de conexión 🔌. Inténtalo de nuevo.";
        }
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = chatInput.value.trim();
        if (!msg) return;

        addMessage(msg, 'user');
        chatInput.value = '';

        showTypingIndicator();

        const reply = await getGeminiResponse(msg);

        removeTypingIndicator();
        addMessage(reply, 'bot');
    });

    // ============================================
    // INTEGRACIÓN BOTÓN BILINGÜE EN HEADER (Banderas)
    // ============================================

    const langStyle = document.createElement('style');
    langStyle.innerHTML = `
        .lang-nav-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-right: 10px; /* Separación de los iconos adyacentes */
            user-select: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            flex-shrink: 0; /* Evita que el botón colapse si el div mengua */
            overflow: hidden;
        }

        .lang-nav-btn img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            pointer-events: none;
        }

        .lang-nav-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.10) translateY(-2px);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* Ocultamiento del anti-estético top bar de Google Translate */
        .skiptranslate iframe, .goog-te-banner-frame { display: none !important; }
        body { top: 0px !important; }
        #google_translate_element { display: none !important; }
        .goog-tooltip, .goog-tooltip:hover { display: none !important; }
        .goog-text-highlight { background-color: transparent !important; border: none !important; box-shadow: none !important; }
    `;
    document.head.appendChild(langStyle);

    // Identificar el idioma activo por la URL/cookie
    const isEnglish = document.cookie.includes('googtrans=/es/en');

    // Inicializar Google Translate Oculto
    const gtDiv = document.createElement('div');
    gtDiv.id = 'google_translate_element';
    document.body.appendChild(gtDiv);

    window.googleTranslateElementInit = function () {
        new google.translate.TranslateElement({ pageLanguage: 'es', includedLanguages: 'en,es', autoDisplay: false }, 'google_translate_element');
    };

    const gtScript = document.createElement('script');
    gtScript.type = 'text/javascript';
    gtScript.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(gtScript);

    // Insertar el botón en la barra superior existente
    const userActionsContainer = document.querySelector('.user-actions');
    if (userActionsContainer) {
        const langBtn = document.createElement('div');
        langBtn.className = 'lang-nav-btn';
        // Mostrar imagen de bandera Americana o Española real (SVGs externos ultraligeros)
        const flagSrc = isEnglish
            ? 'https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/us.svg'
            : 'https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/es.svg';

        langBtn.innerHTML = `<img src="${flagSrc}" alt="${isEnglish ? 'EN' : 'ES'}">`;
        langBtn.title = isEnglish ? 'Change language to Spanish' : 'Cambiar idioma al Inglés';

        // Lo colocamos al principio del contenedor de acciones de usuario
        userActionsContainer.prepend(langBtn);

        langBtn.addEventListener('click', () => {
            // Toggle language via Cookie and Reload
            if (isEnglish) {
                // Restore proper Spanish
                document.cookie = "googtrans=/es/es; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "googtrans=/es/es; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + location.hostname + ";";
                document.cookie = "googtrans=/es/es; path=/;";
                window.location.reload();
            } else {
                // Trigger English using googtrans
                document.cookie = "googtrans=/es/en; path=/;";
                document.cookie = "googtrans=/es/en; path=/; domain=" + location.hostname + ";";
                window.location.reload();
            }
        });
    }

});

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('pokelens-input');
    if (!input) return;

    const loading = document.getElementById('pokelens-loading');
    const resultBox = document.getElementById('pokelens-result');
    const cartas = document.querySelectorAll('.carta, .carta-row');

    // Mismo API KEY que usa el chatbot (Flash)
    const GEMINI_API_KEY = "AIzaSyAUdeHqIAdT4O48tnVglygfcRikZeDmYv8";

    input.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        // Reset state
        resultBox.style.display = 'none';
        cartas.forEach(c => {
            c.style.display = 'flex'; // Vuelve a mostrar todas las cartas
            c.style.border = '';
            c.style.boxShadow = '';
            c.style.transform = '';
        });

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.onload = async () => {
                // Comprimir la imagen usando Canvas para evitar errores "Failed to fetch" por payloads gigantes
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                const MAX_SIZE = 800; // Resolución óptima para IA

                if (width > height) {
                    if (width > MAX_SIZE) {
                        height *= MAX_SIZE / width;
                        width = MAX_SIZE;
                    }
                } else {
                    if (height > MAX_SIZE) {
                        width *= MAX_SIZE / height;
                        height = MAX_SIZE;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Obtener base64 comprimido (JPEG 80% calidad)
                const dataURL = canvas.toDataURL('image/jpeg', 0.8);
                const base64String = dataURL.split(',')[1];
                const mimeType = 'image/jpeg';

                loading.style.display = 'block';

                try {
                    const prompt = "Eres un experto de la tienda PokePimas TCG. Identifica el nombre principal del Pokémon, objeto o personaje de esta carta usando la imagen proporcionada. Tu única tarea es devolver el nombre (y el subtitulo tipo VMAX, VSTAR, EX, etc. si lo tiene). No devuelvas ningún texto extra, solo el nombre. Ejemplo de salida: 'Charizard VMAX' o 'Pikachu'.";

                    const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${GEMINI_API_KEY}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            contents: [{
                                parts: [
                                    { text: prompt },
                                    {
                                        inline_data: {
                                            mime_type: mimeType,
                                            data: base64String
                                        }
                                    }
                                ]
                            }]
                        })
                    });

                    if (!response.ok) {
                        const errText = await response.text();
                        console.error("API Error details:", errText);
                        throw new Error("Error en la conexión con la IA (" + response.status + ")");
                    }

                    const data = await response.json();
                    let cardName = "";
                    if (data.candidates && data.candidates[0].content && data.candidates[0].content.parts[0].text) {
                        cardName = data.candidates[0].content.parts[0].text.trim().toLowerCase();
                    }

                    if (!cardName) throw new Error("No pudimos identificar la carta de la imagen.");

                    loading.style.display = 'none';

                    // Mostrar el nombre identificado (saneado de punto finales etc)
                    cardName = cardName.replace(/[^\w\s-]/gi, '').trim();

                    let foundCardsCount = 0;

                    // Filtrar en el DOM las cartas que coincidan parcialmente con el nombre detectado
                    cartas.forEach(c => {
                        const titleElement = c.querySelector('h4');
                        if (titleElement) {
                            const title = titleElement.textContent.toLowerCase();

                            // Busca si la respuesta de Gemini está en el titulo o al reves
                            let nameParts = cardName.split(' ');
                            let isMatch = title.includes(cardName) || cardName.includes(title);
                            if (!isMatch && nameParts.length > 0) {
                                isMatch = title.includes(nameParts[0]);
                            }

                            if (isMatch) {
                                foundCardsCount++;
                                // Destacar la carta en el listado visual
                                c.style.display = 'flex';
                                c.style.border = '2px solid #10b981';
                                c.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.4)';
                                c.style.transform = 'scale(1.02)';
                                c.style.transition = 'all 0.3s';
                            } else {
                                c.style.display = 'none'; // Ocultar las que no son
                            }
                        }
                    });

                    // Presentar el resultado final en el contenedor dedicado
                    resultBox.style.display = 'block';

                    let answerHTML = `
                    <div style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); border-top: 2px solid #3b82f6; border-radius: 20px; padding: 25px 30px; margin-bottom: 40px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4), inset 0 0 20px rgba(59, 130, 246, 0.1); animation: slideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); color: #f8fafc; display: flex; flex-direction: column; gap: 20px;">
                        <style>
                            @keyframes slideDown { from { opacity: 0; transform: translateY(-20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
                        </style>
                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 15px; flex-wrap: wrap; gap: 15px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.5);">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                </div>
                                <div>
                                    <h3 style="margin: 0; font-size: 1.25rem; font-family: 'Montserrat', sans-serif; font-weight: 800; color: white;">Resultado IA</h3>
                                    <p style="margin: 2px 0 0 0; color: #94a3b8; font-size: 0.85rem;">Análisis completado</p>
                                </div>
                            </div>
                            <div style="background: rgba(15, 23, 42, 0.6); padding: 8px 18px; border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.05);">
                                <span style="color: #94a3b8; font-size: 0.85rem;">Has escaneado:</span> <strong style="color: #fff; text-transform: capitalize; font-size: 1rem; letter-spacing: 0.5px;">${cardName}</strong>
                            </div>
                        </div>
                    `;

                    if (foundCardsCount > 0) {
                        answerHTML += `
                            <div style="background: linear-gradient(to right, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.05)); border-left: 4px solid #10b981; padding: 15px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <h4 style="color: #10b981; margin: 0 0 5px 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        ¡Match Encontrado!
                                    </h4>
                                    <p style="margin: 0; color: #cbd5e1; font-size: 0.95rem;">Hemos destacado <strong>${foundCardsCount} variante(s)</strong> en el catálogo de abajo.</p>
                                </div>
                                <button onclick="window.location.reload()" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: white; padding: 10px 20px; border-radius: 50px; cursor: pointer; transition: all 0.3s; font-size: 0.9rem; font-weight: 600;" onmouseover="this.style.background='rgba(59, 130, 246, 0.2)'; this.style.borderColor='#3b82f6';" onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.2)';">
                                    Restablecer Catálogo ✖
                                </button>
                            </div>
                        </div>
                        `;
                    } else {
                        answerHTML += `
                            <div style="background: linear-gradient(to right, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05)); border-left: 4px solid #ef4444; padding: 15px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <h4 style="color: #ef4444; margin: 0 0 5px 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                        Sin Stock
                                    </h4>
                                    <p style="margin: 0; color: #cbd5e1; font-size: 0.95rem;">Lamentablemente, no tenemos disponibilidad de esta carta en ninguna variante.</p>
                                </div>
                                <button onclick="window.location.reload()" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: white; padding: 10px 20px; border-radius: 50px; cursor: pointer; transition: all 0.3s; font-size: 0.9rem; font-weight: 600;" onmouseover="this.style.background='rgba(59, 130, 246, 0.2)'; this.style.borderColor='#3b82f6';" onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.2)';">
                                    Seguir navegando ✖
                                </button>
                            </div>
                        </div>
                        `;
                    }

                    resultBox.innerHTML = answerHTML;

                    // Hacer scroll suave hacia los resultados para que el usuario los vea instantáneamente
                    resultBox.scrollIntoView({ behavior: 'smooth', block: 'center' });

                } catch (err) {
                    loading.style.display = 'none';
                    console.error(err);
                    cartas.forEach(c => c.style.display = 'flex'); // Restaurar
                    resultBox.style.display = 'block';
                    resultBox.innerHTML = `
                    <div style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); padding: 20px; border-left: 4px solid #ef4444; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideDown 0.3s ease-out; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap;">
                        <div>
                            <span style="font-weight: bold; color: #ef4444; font-size: 1.1rem;">Error de IA</span>
                            <p style="margin: 5px 0 0 0; color: #f8fafc; font-size: 0.9rem;">${err.message}</p>
                        </div>
                        <button onclick="document.getElementById('pokelens-result').style.display='none'" style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 8px 15px; border-radius: 5px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='#ef4444'; this.style.color='white'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.color='#ef4444'">Cerrar</button>
                    </div>
                `;
                }
            };
            // Iniciar la carga de la imagen para comprimirla
            img.src = event.target.result;
        };
    });
});

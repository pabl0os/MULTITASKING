<x-layouts.app>
    <x-slot:header>
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Traductor</h1>
        </div>
        <p class="text-sm text-slate-500">Traduce texto instantáneamente usando Google Translate.</p>
    </x-slot:header>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col md:flex-row h-[600px]">
        <!-- Source Side -->
        <div class="flex-1 flex flex-col border-b md:border-b-0 md:border-r border-slate-200">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex space-x-2">
                    <select id="source_lang" class="text-sm font-semibold text-slate-700 bg-transparent border-none focus:ring-0 cursor-pointer hover:bg-slate-200/50 rounded-lg py-1.5 px-2 transition-colors">
                        <option value="auto">Detectar idioma</option>
                        <option value="es">Español</option>
                        <option value="en">Inglés</option>
                        <option value="pt">Portugués</option>
                        <option value="fr">Francés</option>
                        <option value="de">Alemán</option>
                        <option value="it">Italiano</option>
                        <option value="ru">Ruso</option>
                        <option value="zh-CN">Chino (Simplificado)</option>
                    </select>
                </div>
            </div>
            <div class="flex-1 relative p-4 bg-white">
                <textarea id="source_text" placeholder="Ingresa el texto a traducir..." class="w-full h-full resize-none border-none focus:ring-0 text-xl text-slate-800 placeholder-slate-400 bg-transparent" spellcheck="false"></textarea>
                <div class="absolute bottom-4 right-4 text-xs font-medium text-slate-400">
                    <span id="char_count">0</span> / 5000
                </div>
            </div>
        </div>

        <!-- Target Side -->
        <div class="flex-1 flex flex-col bg-slate-50/30">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex space-x-2">
                    <select id="target_lang" class="text-sm font-bold text-primary bg-transparent border-none focus:ring-0 cursor-pointer hover:bg-blue-50 rounded-lg py-1.5 px-2 transition-colors">
                        <option value="en">Inglés</option>
                        <option value="es">Español</option>
                        <option value="pt">Portugués</option>
                        <option value="fr">Francés</option>
                        <option value="de">Alemán</option>
                        <option value="it">Italiano</option>
                        <option value="ru">Ruso</option>
                        <option value="zh-CN">Chino (Simplificado)</option>
                    </select>
                </div>
            </div>
            <div class="flex-1 relative p-4">
                <!-- Loading Overlay -->
                <div id="loading_overlay" class="absolute inset-0 flex items-center justify-center bg-slate-50/80 z-10 hidden">
                    <div class="w-8 h-8 border-4 border-slate-200 border-t-primary rounded-full animate-spin"></div>
                </div>
                
                <textarea id="target_text" readonly placeholder="Traducción" class="w-full h-full resize-none border-none focus:ring-0 text-xl text-slate-800 placeholder-slate-400 bg-transparent"></textarea>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sourceText = document.getElementById('source_text');
            const targetText = document.getElementById('target_text');
            const sourceLang = document.getElementById('source_lang');
            const targetLang = document.getElementById('target_lang');
            const charCount = document.getElementById('char_count');
            const loadingOverlay = document.getElementById('loading_overlay');
            
            let debounceTimer;

            const performTranslation = async () => {
                const text = sourceText.value.trim();
                
                if (!text) {
                    targetText.value = '';
                    return;
                }

                loadingOverlay.classList.remove('hidden');

                try {
                    const response = await fetch('{{ route("translation.translate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            text: text,
                            source_lang: sourceLang.value,
                            target_lang: targetLang.value
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        targetText.value = data.translated_text;
                    } else {
                        targetText.value = data.message || 'Error en la traducción.';
                    }
                } catch (error) {
                    targetText.value = 'Error de red al intentar traducir.';
                    console.error('Translation error:', error);
                } finally {
                    loadingOverlay.classList.add('hidden');
                }
            };

            const onInput = () => {
                const text = sourceText.value;
                charCount.textContent = text.length;
                
                if (text.length > 5000) {
                    sourceText.value = text.slice(0, 5000);
                    charCount.textContent = 5000;
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performTranslation, 600);
            };

            sourceText.addEventListener('input', onInput);
            sourceLang.addEventListener('change', performTranslation);
            targetLang.addEventListener('change', performTranslation);
        });
    </script>
</x-layouts.app>

{{-- Assistant IA SENDAPTNAPT — widget flottant (vanilla JS, sans Alpine) --}}
@auth
@if(config('assistant.enabled', true))
<div id="sen-assistant-root" style="position:fixed;bottom:20px;right:20px;z-index:2147483647;font-family:'Open Sans',sans-serif;">
    <div id="sen-assistant-panel" style="display:none;margin-bottom:12px;width:min(100vw - 2rem,380px);height:min(70vh,520px);flex-direction:column;overflow:hidden;border-radius:16px;border:1px solid rgba(255,255,255,.2);box-shadow:0 25px 50px rgba(0,0,0,.45);background:linear-gradient(180deg,#2B1444 0%,#1a0c2e 40%,#12081f 100%);">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:12px 16px;color:#fff;background:linear-gradient(90deg,#2B1444 0%,#B3006C 70%,#E87400 100%);">
            <div style="min-width:0;">
                <p style="margin:0;font-size:14px;font-weight:600;font-family:Rajdhani,sans-serif;">Assistant SENDAPTNAPT</p>
                <p id="sen-assistant-mode" style="margin:0;font-size:11px;opacity:.85;">Mode local</p>
            </div>
            <button type="button" id="sen-assistant-close" aria-label="Fermer" style="background:transparent;border:0;color:#fff;cursor:pointer;padding:4px 8px;font-size:18px;line-height:1;">×</button>
        </div>

        <div id="sen-assistant-suggestions" style="display:flex;flex-wrap:wrap;gap:6px;padding:12px 12px 0;"></div>

        <div id="sen-assistant-messages" style="flex:1;min-height:0;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px;"></div>

        <form id="sen-assistant-form" style="border-top:1px solid rgba(255,255,255,.1);padding:12px;display:flex;gap:8px;">
            <input id="sen-assistant-input" type="text" maxlength="2000" placeholder="Posez votre question…" style="flex:1;min-width:0;border-radius:12px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.1);color:#fff;padding:8px 12px;font-size:14px;outline:none;">
            <button type="submit" style="border:0;border-radius:12px;padding:8px 12px;font-size:14px;font-weight:600;color:#fff;cursor:pointer;background:linear-gradient(135deg,#B3006C,#E87400);">Envoyer</button>
        </form>
    </div>

    <button type="button" id="sen-assistant-fab" title="Assistant SENDAPTNAPT" aria-label="Ouvrir l’assistant" style="display:flex;height:56px;width:56px;align-items:center;justify-content:center;border-radius:9999px;border:2px solid rgba(255,255,255,.35);color:#fff;cursor:pointer;box-shadow:0 10px 25px rgba(43,20,68,.5);background:linear-gradient(135deg,#2B1444 0%,#B3006C 55%,#E87400 100%);">
        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
    </button>
</div>

<script>
(function () {
    const statusUrl = @json(route('assistant.status'));
    const chatUrl = @json(route('assistant.chat'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const root = document.getElementById('sen-assistant-root');
    const panel = document.getElementById('sen-assistant-panel');
    const fab = document.getElementById('sen-assistant-fab');
    const closeBtn = document.getElementById('sen-assistant-close');
    const form = document.getElementById('sen-assistant-form');
    const input = document.getElementById('sen-assistant-input');
    const messagesEl = document.getElementById('sen-assistant-messages');
    const modeEl = document.getElementById('sen-assistant-mode');
    const suggestionsEl = document.getElementById('sen-assistant-suggestions');

    if (!root || !panel || !fab) return;

    let open = false;
    let loading = false;
    const messages = [{
        role: 'assistant',
        content: 'Bonjour ! Je peux vous aider sur le fonctionnement de SENDAPTNAPT, vos DAPT, NAPT et la file d’attente selon votre rôle.'
    }];
    const suggestions = [
        'Comment créer une DAPT ?',
        'Comment faire une diffusion ?',
        'Que dois-je traiter ?'
    ];

    function bubble(role, content) {
        const wrap = document.createElement('div');
        wrap.style.display = 'flex';
        wrap.style.justifyContent = role === 'user' ? 'flex-end' : 'flex-start';
        const b = document.createElement('div');
        b.style.maxWidth = '90%';
        b.style.whiteSpace = 'pre-wrap';
        b.style.borderRadius = '16px';
        b.style.padding = '8px 12px';
        b.style.fontSize = '14px';
        b.style.lineHeight = '1.45';
        b.style.color = '#fff';
        if (role === 'user') {
            b.style.background = '#B3006C';
            b.style.borderBottomRightRadius = '4px';
        } else {
            b.style.background = 'rgba(255,255,255,.1)';
            b.style.border = '1px solid rgba(255,255,255,.1)';
            b.style.borderBottomLeftRadius = '4px';
        }
        b.textContent = content;
        wrap.appendChild(b);
        return wrap;
    }

    function renderMessages() {
        messagesEl.innerHTML = '';
        messages.forEach(m => messagesEl.appendChild(bubble(m.role, m.content)));
        if (loading) {
            messagesEl.appendChild(bubble('assistant', 'Réflexion…'));
        }
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function renderSuggestions() {
        suggestionsEl.innerHTML = '';
        suggestions.forEach(text => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = text;
            btn.style.cssText = 'border-radius:9999px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:rgba(255,255,255,.9);padding:4px 10px;font-size:11px;cursor:pointer;';
            btn.addEventListener('click', () => send(text));
            suggestionsEl.appendChild(btn);
        });
    }

    function setOpen(v) {
        open = v;
        panel.style.display = open ? 'flex' : 'none';
        if (open) loadStatus();
    }

    async function loadStatus() {
        try {
            const res = await fetch(statusUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();
            if (data.mode === 'gemini' || data.configured) {
                modeEl.textContent = 'Clé Gemini OK (réseau Google AI parfois bloqué)';
            } else if (data.mode === 'disabled') {
                modeEl.textContent = 'Désactivé';
            } else {
                modeEl.textContent = 'Mode local (sans clé Gemini)';
            }
        } catch (e) {}
    }

    async function send(preset) {
        const text = (preset || input.value || '').trim();
        if (!text || loading) return;
        input.value = '';
        messages.push({ role: 'user', content: text });
        loading = true;
        renderMessages();

        const history = messages.slice(0, -1).slice(-6).map(m => ({ role: m.role, content: m.content }));

        try {
            const res = await fetch(chatUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ message: text, history })
            });
            const data = await res.json().catch(() => ({}));
            if (data.mode === 'gemini') modeEl.textContent = 'Assisté par Gemini';
            else if (data.mode === 'offline') modeEl.textContent = 'Mode local (réseau Gemini injoignable)';
            const fallbackMsg = res.ok
                ? 'Réponse vide.'
                : (data.error || data.message || 'Erreur lors de la réponse. Réessayez dans une minute.');
            messages.push({
                role: 'assistant',
                content: data.reply || fallbackMsg
            });
        } catch (e) {
            messages.push({ role: 'assistant', content: 'Délai dépassé ou réseau coupé. Réessayez — le mode local répondra ensuite.' });
        } finally {
            loading = false;
            renderMessages();
        }
    }

    fab.addEventListener('click', () => setOpen(!open));
    closeBtn.addEventListener('click', () => setOpen(false));
    form.addEventListener('submit', (e) => { e.preventDefault(); send(); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && open) setOpen(false);
    });

    renderSuggestions();
    renderMessages();
})();
</script>
@endif
@endauth

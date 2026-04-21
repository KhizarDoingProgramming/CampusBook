// Dark Mode Logic
document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const root = document.documentElement;
    const icon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;

    // Check local storage for theme preference
    const currentTheme = localStorage.getItem('theme') || 'light';
    root.setAttribute('data-theme', currentTheme);
    if (icon) {
        icon.className = currentTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const newTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            icon.className = newTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        });
    }

    // Chat Widget Logic
    window.openChat = function(name, avatar) {
        let chatWidget = document.getElementById('chat-widget');
        if (!chatWidget) {
            // Create chat widget if it doesn't exist
            chatWidget = document.createElement('div');
            chatWidget.id = 'chat-widget';
            chatWidget.className = 'chat-widget open';
            chatWidget.innerHTML = `
                <div class="chat-header" onclick="document.getElementById('chat-widget').classList.toggle('open')">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <img src="" id="chat-avatar" style="width:28px; height:28px; border-radius:50%;">
                        <span id="chat-name" style="font-weight:600;"></span>
                    </div>
                    <i class="fa-solid fa-times" onclick="event.stopPropagation(); document.getElementById('chat-widget').remove()"></i>
                </div>
                <div class="chat-body" id="chat-body">
                    <div class="msg-bubble msg-received">Hey! How are you?</div>
                </div>
                <div class="chat-input-container">
                    <input type="text" placeholder="Aa" onkeypress="if(event.key === 'Enter') sendMsg(this)">
                </div>
            `;
            document.body.appendChild(chatWidget);
        } else {
            chatWidget.classList.add('open');
        }
        document.getElementById('chat-name').innerText = name;
        document.getElementById('chat-avatar').src = avatar;
    };

    window.sendMsg = function(input) {
        if (!input.value.trim()) return;
        const body = document.getElementById('chat-body');
        const msg = document.createElement('div');
        msg.className = 'msg-bubble msg-sent';
        msg.innerText = input.value;
        body.appendChild(msg);
        input.value = '';
        body.scrollTop = body.scrollHeight;
        
        // Auto reply simulation
        setTimeout(() => {
            const reply = document.createElement('div');
            reply.className = 'msg-bubble msg-received';
            reply.innerText = "That's awesome! Let me get back to you later.";
            body.appendChild(reply);
            body.scrollTop = body.scrollHeight;
        }, 1500);
    };
});

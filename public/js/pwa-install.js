/**
 * PWA Install Prompt Handler
 * Captures and manages the beforeinstallprompt event for consistent behavior
 */

let deferredPrompt = null;
let isInstalled = false;

// Check if app is already installed
window.addEventListener('appinstalled', () => {
    console.log('[FK365] App installed successfully');
    isInstalled = true;
    deferredPrompt = null;
    localStorage.setItem('fk365_installed', 'true');
    hideInstallPrompt();
});

// Capture the beforeinstallprompt event
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent the mini-infobar from appearing
    e.preventDefault();
    // Store the event for later use
    deferredPrompt = e;
    console.log('[FK365] Install prompt captured and deferred');
    // Show install prompt on every new session
    showInstallPrompt();
});

// Create and manage install prompt UI
function createInstallPromptUI() {
    const existing = document.getElementById('pwa-install-prompt');
    if (existing) return existing;

    const banner = document.createElement('div');
    banner.id = 'pwa-install-prompt';
    banner.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background: linear-gradient(135deg, var(--accent, #e11d48) 0%, var(--accent2, #f43f5e) 100%);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        color: white;
        z-index: 1000;
        display: none;
        animation: slideUp 0.3s ease;
        font-family: system-ui, -apple-system, sans-serif;
    `;

    banner.innerHTML = `
        <div style="display: flex; align-items: flex-start; gap: 12px; justify-content: space-between;">
            <div style="flex: 1;">
                <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">📱 FireKontrol 365 installieren</div>
                <div style="font-size: 12px; opacity: 0.95;">Schneller Zugriff von deinem Startbildschirm</div>
            </div>
            <div style="display: flex; gap: 8px; flex-shrink: 0;">
                <button id="pwa-install-btn" style="
                    padding: 6px 14px;
                    background: rgba(255,255,255,0.3);
                    border: 1px solid rgba(255,255,255,0.5);
                    color: white;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                ">Installieren</button>
                <button id="pwa-dismiss-btn" style="
                    padding: 6px 12px;
                    background: transparent;
                    border: none;
                    color: white;
                    font-size: 16px;
                    cursor: pointer;
                    opacity: 0.8;
                ">✕</button>
            </div>
        </div>
    `;

    document.body.appendChild(banner);

    // Install button handler
    document.getElementById('pwa-install-btn').addEventListener('click', installApp);

    // Dismiss button handler
    document.getElementById('pwa-dismiss-btn').addEventListener('click', dismissInstallPrompt);

    // Add slide animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);

    return banner;
}

function showInstallPrompt() {
    if (isInstalled || !deferredPrompt) return;
    // Don't show if already dismissed in this session
    if (sessionStorage.getItem('fk365_install_dismissed_session')) return;
    
    const banner = createInstallPromptUI();
    banner.style.display = 'flex';
    console.log('[FK365] Install prompt shown');
}

function hideInstallPrompt() {
    const banner = document.getElementById('pwa-install-prompt');
    if (banner) {
        banner.style.display = 'none';
    }
}

function dismissInstallPrompt() {
    // Only hide for this session (not permanently)
    sessionStorage.setItem('fk365_install_dismissed_session', 'true');
    hideInstallPrompt();
    deferredPrompt = null;
    console.log('[FK365] Install prompt dismissed for this session (will reappear on next visit)');
}

async function installApp() {
    if (!deferredPrompt) {
        console.log('[FK365] Install prompt not available');
        return;
    }

    // Show the native install prompt
    deferredPrompt.prompt();
    
    // Wait for user response
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`[FK365] User response: ${outcome}`);

    // Clear the deferred prompt
    deferredPrompt = null;

    // Hide banner regardless of user choice
    hideInstallPrompt();
}

// Show install prompt again if user wants to (store function globally)
window.showPWAInstallPrompt = () => {
    if (isInstalled) {
        alert('FireKontrol 365 ist bereits installiert.');
        return;
    }
    sessionStorage.removeItem('fk365_install_dismissed_session');
    showInstallPrompt();
};

// Check if already installed on page load
window.addEventListener('load', () => {
    if (localStorage.getItem('fk365_installed') === 'true') {
        isInstalled = true;
        console.log('[FK365] App is already installed');
    }
});

console.log('[FK365] PWA install handler loaded');

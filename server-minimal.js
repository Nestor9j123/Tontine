// Serveur Node.js minimal pour Tontine App
const express = require('express');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

console.log('🚀 Tontine App - Serveur Node.js minimal');
console.log('🔒 HTTPS: Automatique sur Render');
console.log('📱 PWA: Ready to install!');

// Servir les fichiers statiques
app.use(express.static('public'));

// Routes principales
app.get('/', (req, res) => {
    res.send(`
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tontine App - PWA</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Tontine App">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .container { 
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 3rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            max-width: 500px;
            margin: 20px;
        }
        .logo { font-size: 4rem; margin-bottom: 1rem; }
        h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        .status { 
            background: rgba(34, 197, 94, 0.2);
            padding: 1rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .features { 
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }
        .feature {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .install-prompt {
            background: rgba(59, 130, 246, 0.2);
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 1rem;
            border-radius: 10px;
            margin-top: 2rem;
        }
        @media (max-width: 640px) {
            .features { grid-template-columns: 1fr; }
            .container { padding: 2rem 1rem; }
            h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">💰</div>
        <h1>Tontine App</h1>
        <p>Application de gestion de tontines</p>
        
        <div class="status">
            <strong>✅ PWA Ready</strong><br>
            <small>HTTPS • Installable • Mode hors ligne</small>
        </div>
        
        <div class="features">
            <div class="feature">
                <strong>📱 Mobile</strong><br>
                <small>Installation native</small>
            </div>
            <div class="feature">
                <strong>💻 Desktop</strong><br>
                <small>App standalone</small>
            </div>
            <div class="feature">
                <strong>🔒 Sécurisé</strong><br>
                <small>HTTPS automatique</small>
            </div>
            <div class="feature">
                <strong>⚡ Rapide</strong><br>
                <small>Cache intelligent</small>
            </div>
        </div>
        
        <div class="install-prompt">
            <strong>📥 Installation PWA</strong><br>
            <small>Recherchez le bouton d'installation dans votre navigateur</small>
        </div>
    </div>

    <script>
        // PWA Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(() => console.log('✅ Service Worker registered'))
                .catch(() => console.log('❌ Service Worker failed'));
        }

        // PWA Install Prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            const installBtn = document.createElement('button');
            installBtn.innerHTML = '📱 Installer l\\'app';
            installBtn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; background: #2563eb; color: white; border: none; padding: 12px 20px; border-radius: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); cursor: pointer; font-weight: bold; z-index: 1000;';
            
            installBtn.onclick = async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    deferredPrompt = null;
                    installBtn.remove();
                }
            };
            
            document.body.appendChild(installBtn);
        });

        // Hide install button after successful install
        window.addEventListener('appinstalled', () => {
            const installBtn = document.querySelector('button');
            if (installBtn) installBtn.remove();
        });
    </script>
</body>
</html>
    `);
});

// Health check
app.get('/health', (req, res) => {
    res.json({ 
        status: 'OK', 
        pwa: true, 
        https: req.secure,
        timestamp: new Date().toISOString()
    });
});

// Démarrer le serveur
app.listen(PORT, '0.0.0.0', () => {
    console.log('✅ Serveur démarré sur le port ' + PORT);
    console.log('🌐 URL: http://localhost:' + PORT);
    console.log('📱 PWA: Prêt à installer!');
});

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

// Servir un manifest simplifié
app.get('/manifest.json', (req, res) => {
    const manifest = {
        "name": "Tontine App - Gestion de Tontines",
        "short_name": "Tontine",
        "description": "Application de gestion complète de tontines",
        "start_url": "/",
        "id": "tontine-app-pwa",
        "display": "standalone",
        "background_color": "#1e40af",
        "theme_color": "#2563eb",
        "orientation": "portrait-primary",
        "scope": "/",
        "lang": "fr",
        "categories": ["business", "finance", "productivity"],
        "icons": [
            {
                "src": "/icons/icon-192.png",
                "sizes": "192x192",
                "type": "image/png",
                "purpose": "maskable any"
            },
            {
                "src": "/icons/icon-512.png", 
                "sizes": "512x512",
                "type": "image/png",
                "purpose": "maskable any"
            }
        ]
    };
    
    res.setHeader('Content-Type', 'application/json');
    res.setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
    res.setHeader('Pragma', 'no-cache');
    res.setHeader('Expires', '0');
    res.json(manifest);
});

// Générer les icônes PWA à la volée (solution temporaire)
app.get('/icons/icon-:size.png', (req, res) => {
    const size = parseInt(req.params.size);
    
    if (isNaN(size) || size < 16 || size > 1024) {
        return res.status(400).send('Invalid size');
    }
    
    // PNG bleu simple 1x1 avec couleur Tontine 
    const bluePngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9wBgAEhAJ1AHdMKgAAAABJRU5ErkJggg==';
    const pngBuffer = Buffer.from(bluePngBase64, 'base64');
    
    res.setHeader('Content-Type', 'image/png');
    res.setHeader('Cache-Control', 'public, max-age=86400');
    res.setHeader('Content-Length', pngBuffer.length);
    res.send(pngBuffer);
});

// Route alternative pour icônes avec dimensions
app.get('/icons/icon-:width:x:height.png', (req, res) => {
    const width = parseInt(req.params.width);
    const height = parseInt(req.params.height);
    
    if (isNaN(width) || isNaN(height) || width < 16 || height < 16) {
        return res.status(400).send('Invalid dimensions');
    }
    
    // Image PNG 1x1 pixel en base64
    const pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQIHWNgAAIAAAUAAY27m/MAAAAASUVORK5CYII=';
    const pngBuffer = Buffer.from(pngBase64, 'base64');
    
    res.setHeader('Content-Type', 'image/png');
    res.setHeader('Cache-Control', 'public, max-age=86400');
    res.send(pngBuffer);
});

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
    <meta name="mobile-web-app-capable" content="yes">
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
            <small>Recherchez le bouton d'installation dans votre navigateur</small><br>
            <button onclick="forceInstall()" style="margin-top: 10px; padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">
                📱 Installer maintenant
            </button>
        </div>
    </div>

    <script>
        // PWA Service Worker avec debug
        console.log('🔍 DIAGNOSTIC PWA:');
        console.log('📱 Navigateur:', navigator.userAgent);
        console.log('🔒 HTTPS:', window.location.protocol === 'https:');
        console.log('⚙️ Service Worker supporté:', 'serviceWorker' in navigator);
        
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(() => {
                    console.log('✅ Service Worker registered');
                    // Test du manifest
                    fetch('/manifest.json')
                        .then(response => {
                            if (response.ok) {
                                console.log('✅ Manifest accessible');
                            } else {
                                console.log('❌ Manifest introuvable');
                            }
                        })
                        .catch(() => console.log('❌ Erreur manifest'));
                })
                .catch((error) => {
                    console.log('❌ Service Worker failed:', error);
                });
        } else {
            console.log('❌ Service Worker non supporté');
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

        // Fonction d'installation forcée
        window.forceInstall = function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('✅ PWA installée via prompt');
                    } else {
                        console.log('❌ Installation PWA refusée');
                    }
                    deferredPrompt = null;
                });
            } else {
                // Créer un raccourci desktop alternatif
                const isChrome = navigator.userAgent.includes('Chrome');
                const isFirefox = navigator.userAgent.includes('Firefox');
                const isSafari = navigator.userAgent.includes('Safari') && !navigator.userAgent.includes('Chrome');
                
                let instructions = 'INSTRUCTIONS D\\'INSTALLATION:\\n\\n';
                
                if (isChrome) {
                    instructions += '🔧 CHROME - Méthodes alternatives:\\n' +
                        '1. Ctrl+D → Ajouter aux favoris → Cocher "Applications"\\n' +
                        '2. Créer un raccourci desktop:\\n' +
                        '   - Clic droit sur bureau → Nouveau → Raccourci\\n' +
                        '   - URL: ' + window.location.href + '\\n' +
                        '   - Nom: Tontine App\\n\\n' +
                        '3. Mode Kiosque Chrome:\\n' +
                        '   - chrome.exe --app=' + window.location.href;
                } else if (isFirefox) {
                    instructions += '🦊 FIREFOX:\\n' +
                        '1. Créer un raccourci desktop:\\n' +
                        '   - Clic droit sur bureau → Nouveau → Raccourci\\n' +
                        '   - URL: ' + window.location.href + '\\n' +
                        '   - Nom: Tontine App';
                } else if (isSafari) {
                    instructions += '🍎 SAFARI:\\n' +
                        '1. Bouton Partager (📤)\\n' +
                        '2. "Ajouter à l\\'écran d\\'accueil"';
                } else {
                    instructions += '🌐 NAVIGATEUR GÉNÉRIQUE:\\n' +
                        '1. Créer un raccourci desktop:\\n' +
                        '   - Clic droit sur bureau → Nouveau → Raccourci\\n' +
                        '   - URL: ' + window.location.href + '\\n' +
                        '   - Nom: Tontine App';
                }
                
                alert(instructions);
            }
        };

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

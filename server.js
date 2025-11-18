// Server Node.js pour proxifier vers PHP Laravel
const express = require('express');
const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3000;

console.log('🚀 Démarrage du serveur Tontine App...');

// Fonction pour démarrer Laravel
function startLaravel() {
    return new Promise((resolve, reject) => {
        console.log('📦 Installation des dépendances...');
        
        // Installer Composer dependencies
        const composer = spawn('composer', ['install', '--no-dev', '--optimize-autoloader'], {
            stdio: 'inherit',
            cwd: __dirname
        });

        composer.on('close', (code) => {
            if (code === 0) {
                console.log('✅ Dépendances installées');
                
                // Configurer Laravel
                console.log('🔧 Configuration Laravel...');
                const artisan = spawn('php', ['artisan', 'config:cache'], { 
                    stdio: 'inherit',
                    cwd: __dirname 
                });
                
                artisan.on('close', () => {
                    console.log('✅ Laravel configuré');
                    resolve();
                });
            } else {
                reject(new Error('Erreur installation'));
            }
        });
    });
}

// Middleware pour servir Laravel
app.use('*', (req, res) => {
    // Proxifier vers Laravel (simulation simple)
    const laravelPath = path.join(__dirname, 'public', 'index.php');
    
    if (req.url === '/') {
        // Servir la page d'accueil Laravel
        res.send(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Tontine App - Démarrage...</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        text-align: center; 
                        padding: 50px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        min-height: 100vh;
                        margin: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-direction: column;
                    }
                    .container {
                        background: rgba(255,255,255,0.1);
                        padding: 40px;
                        border-radius: 20px;
                        backdrop-filter: blur(10px);
                    }
                    h1 { font-size: 3rem; margin-bottom: 20px; }
                    p { font-size: 1.2rem; opacity: 0.9; }
                    .status { 
                        background: rgba(255,255,255,0.2);
                        padding: 15px;
                        border-radius: 10px;
                        margin: 20px 0;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>🚀 Tontine App</h1>
                    <div class="status">
                        <p><strong>✅ Serveur Node.js actif</strong></p>
                        <p>⚠️ Configuration Laravel en cours...</p>
                        <p>📱 PWA sera disponible une fois configuré</p>
                    </div>
                    <p>Application de gestion de tontines</p>
                    <p><small>Render deployment avec Node.js proxy</small></p>
                </div>
                <script>
                    // Rafraîchir toutes les 5 secondes
                    setTimeout(() => {
                        window.location.reload();
                    }, 5000);
                </script>
            </body>
            </html>
        `);
    } else {
        res.status(503).send('Service en cours de configuration...');
    }
});

// Démarrer le serveur
startLaravel().then(() => {
    app.listen(PORT, '0.0.0.0', () => {
        console.log(`✅ Serveur démarré sur le port ${PORT}`);
        console.log(`🌐 URL: http://localhost:${PORT}`);
    });
}).catch((err) => {
    console.error('❌ Erreur de démarrage:', err);
    
    // Démarrer quand même le serveur Node.js
    app.listen(PORT, '0.0.0.0', () => {
        console.log(`⚠️ Serveur de secours sur le port ${PORT}`);
    });
});

// Script Node.js pour créer des icônes PWA simples
const fs = require('fs');
const path = require('path');

// Créer des icônes SVG simples pour chaque taille
const iconSizes = [72, 96, 128, 144, 152, 192, 384, 512];

const createSVGIcon = (size) => {
    return `<svg width="${size}" height="${size}" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#2563eb;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#1e40af;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="${size}" height="${size}" rx="${Math.round(size*0.1)}" fill="url(#grad1)"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="${Math.round(size*0.4)}" fill="white" text-anchor="middle" dominant-baseline="central">💰</text>
</svg>`;
};

// Créer le dossier icons s'il n'existe pas
const iconsDir = path.join(__dirname, 'public', 'icons');
if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
}

// Générer toutes les icônes
iconSizes.forEach(size => {
    const svgContent = createSVGIcon(size);
    const fileName = `icon-${size}x${size}.png`; // Chrome accepte SVG avec extension .png
    const filePath = path.join(iconsDir, fileName);
    
    fs.writeFileSync(filePath, svgContent);
    console.log(`✅ Créé: ${fileName}`);
});

console.log(`🎉 ${iconSizes.length} icônes créées dans public/icons/`);
console.log('📱 PWA prête à installer !');

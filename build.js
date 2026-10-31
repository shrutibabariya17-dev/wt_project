const fs = require('fs');
const path = require('path');

const srcDir = __dirname;
const outDir = path.join(__dirname, 'public');

if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, { recursive: true });
}

// Copy all root project files into public/
const files = fs.readdirSync(srcDir);
for (const file of files) {
    if (file === 'public' || file === '.git' || file === 'node_modules') continue;
    const srcPath = path.join(srcDir, file);
    const stat = fs.statSync(srcPath);
    if (stat.isFile()) {
        fs.copyFileSync(srcPath, path.join(outDir, file));
    }
}

// Ensure manager.php is also accessible as a static HTML file
if (fs.existsSync(path.join(outDir, 'manager.html'))) {
    fs.copyFileSync(path.join(outDir, 'manager.html'), path.join(outDir, 'manager.php'));
}

// Also create lowercase aliases for uppercase files so URLs are case-insensitive
const aliases = [
    ['COURSEENROLLMENT.html', 'courseenrollment.html'],
    ['COURSEENROLLMENT.html', 'course-enrollment.html'],
    ['ADMINSTRATIVE.html', 'administrative.html']
];

for (const [orig, alias] of aliases) {
    const origPath = path.join(outDir, orig);
    if (fs.existsSync(origPath)) {
        fs.copyFileSync(origPath, path.join(outDir, alias));
    }
}

console.log('Build complete! Successfully generated static bundle in /public');


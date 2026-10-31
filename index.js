// index.js - Serverless Entrypoint for Vercel
const http = require('http');
const fs = require('fs');
const path = require('path');

// Pre-read all pages with static string paths so @vercel/nft bundles them into the Lambda container
const PAGES = {
    '/': fs.readFileSync(path.join(__dirname, 'index.html'), 'utf8'),
    '/index.html': fs.readFileSync(path.join(__dirname, 'index.html'), 'utf8'),
    '/index': fs.readFileSync(path.join(__dirname, 'index.html'), 'utf8'),
    '/login.html': fs.readFileSync(path.join(__dirname, 'login.html'), 'utf8'),
    '/login': fs.readFileSync(path.join(__dirname, 'login.html'), 'utf8'),
    '/registration.html': fs.readFileSync(path.join(__dirname, 'registration.html'), 'utf8'),
    '/registration': fs.readFileSync(path.join(__dirname, 'registration.html'), 'utf8'),
    '/dashboard.html': fs.readFileSync(path.join(__dirname, 'dashboard.html'), 'utf8'),
    '/dashboard': fs.readFileSync(path.join(__dirname, 'dashboard.html'), 'utf8'),
    '/courseenrollment.html': fs.readFileSync(path.join(__dirname, 'COURSEENROLLMENT.html'), 'utf8'),
    '/courseenrollment': fs.readFileSync(path.join(__dirname, 'COURSEENROLLMENT.html'), 'utf8'),
    '/course-enrollment.html': fs.readFileSync(path.join(__dirname, 'COURSEENROLLMENT.html'), 'utf8'),
    '/course-enrollment': fs.readFileSync(path.join(__dirname, 'COURSEENROLLMENT.html'), 'utf8'),
    '/campuslife.html': fs.readFileSync(path.join(__dirname, 'campuslife.html'), 'utf8'),
    '/campuslife': fs.readFileSync(path.join(__dirname, 'campuslife.html'), 'utf8'),
    '/events.html': fs.readFileSync(path.join(__dirname, 'events.html'), 'utf8'),
    '/events': fs.readFileSync(path.join(__dirname, 'events.html'), 'utf8'),
    '/faq.html': fs.readFileSync(path.join(__dirname, 'faq.html'), 'utf8'),
    '/faq': fs.readFileSync(path.join(__dirname, 'faq.html'), 'utf8'),
    '/about.html': fs.readFileSync(path.join(__dirname, 'about.html'), 'utf8'),
    '/about': fs.readFileSync(path.join(__dirname, 'about.html'), 'utf8'),
    '/contact.html': fs.readFileSync(path.join(__dirname, 'contact.html'), 'utf8'),
    '/contact': fs.readFileSync(path.join(__dirname, 'contact.html'), 'utf8'),
    '/manager.html': fs.readFileSync(path.join(__dirname, 'manager.html'), 'utf8'),
    '/manager.php': fs.readFileSync(path.join(__dirname, 'manager.html'), 'utf8'),
    '/manager': fs.readFileSync(path.join(__dirname, 'manager.html'), 'utf8'),
    '/administrative.html': fs.readFileSync(path.join(__dirname, 'ADMINSTRATIVE.html'), 'utf8'),
    '/administrative': fs.readFileSync(path.join(__dirname, 'ADMINSTRATIVE.html'), 'utf8'),
    '/style.css': fs.readFileSync(path.join(__dirname, 'style.css'), 'utf8'),
    '/events.json': fs.readFileSync(path.join(__dirname, 'events.json'), 'utf8')
};

const MIME_TYPES = {
    '.html': 'text/html; charset=UTF-8',
    '.css': 'text/css; charset=UTF-8',
    '.js': 'text/javascript; charset=UTF-8',
    '.json': 'application/json; charset=UTF-8',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.svg': 'image/svg+xml'
};

function handler(req, res) {
    let rawPath = req.url.split('?')[0];
    let cleanPath = rawPath.toLowerCase();

    // Remove trailing slash if present
    if (cleanPath.length > 1 && cleanPath.endsWith('/')) {
        cleanPath = cleanPath.slice(0, -1);
    }

    // Direct in-memory lookup
    if (PAGES[cleanPath]) {
        let contentType = 'text/html; charset=UTF-8';
        if (cleanPath.endsWith('.css')) contentType = 'text/css; charset=UTF-8';
        if (cleanPath.endsWith('.json')) contentType = 'application/json; charset=UTF-8';

        res.writeHead(200, {
            'Content-Type': contentType,
            'Cache-Control': 'public, max-age=3600'
        });
        res.end(PAGES[cleanPath]);
        return;
    }

    // Also check original casing
    if (PAGES[rawPath]) {
        res.writeHead(200, {
            'Content-Type': 'text/html; charset=UTF-8',
            'Cache-Control': 'public, max-age=3600'
        });
        res.end(PAGES[rawPath]);
        return;
    }

    // Fallback: Check if file exists on disk
    let filePath = path.join(__dirname, rawPath);
    if (!path.extname(filePath) && fs.existsSync(filePath + '.html')) {
        filePath += '.html';
    }

    if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
        const ext = path.extname(filePath).toLowerCase();
        res.writeHead(200, { 'Content-Type': MIME_TYPES[ext] || 'application/octet-stream' });
        fs.createReadStream(filePath).pipe(res);
        return;
    }

    // Default fallback to index.html
    res.writeHead(200, { 'Content-Type': 'text/html; charset=UTF-8' });
    res.end(PAGES['/']);
}

// Local testing runner
if (require.main === module) {
    const port = process.env.PORT || 3000;
    http.createServer(handler).listen(port, () => {
        console.log(`Server running at http://localhost:${port}/`);
    });
}

module.exports = handler;

const puppeteer = require('puppeteer-core');
const path = require('path');
const fs = require('fs');

async function main() {
    const chromePath = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';
    const executablePath = fs.existsSync(chromePath) ? chromePath : edgePath;

    console.log('Using browser executable:', executablePath);

    const outDir = path.resolve(__dirname, '../docs/screenshots');
    if (!fs.existsSync(outDir)) {
        fs.mkdirSync(outDir, { recursive: true });
    }

    const browser = await puppeteer.launch({
        executablePath,
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-gpu',
            '--disable-dev-shm-usage',
            '--disable-background-networking'
        ],
        defaultViewport: {
            width: 1440,
            height: 900
        }
    });

    const page = await browser.newPage();
    page.setDefaultTimeout(15000);

    const sleep = (ms) => new Promise(r => setTimeout(r, ms));

    // 1. Login Page
    console.log('1. Navigating to login...');
    await page.goto('http://127.0.0.1:8080/login', { waitUntil: 'domcontentloaded' });
    await sleep(1000);
    await page.screenshot({ path: path.join(outDir, '01-login.png') });
    console.log('Saved 01-login.png');

    // 2. Perform Login
    console.log('2. Submitting login credentials...');
    await page.type('input[name="login"]', 'admin');
    await page.type('input[name="password"]', 'password123');
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.click('button[type="submit"]')
    ]);
    console.log('Logged in. Current URL:', page.url());

    // 2. Dashboard
    console.log('3. Capturing 02-dashboard.png...');
    await sleep(1500);
    await page.screenshot({ path: path.join(outDir, '02-dashboard.png') });
    console.log('Saved 02-dashboard.png');

    // 3. Students List
    console.log('4. Navigating to students list...');
    await page.goto('http://127.0.0.1:8080/admin/students', { waitUntil: 'domcontentloaded' });
    await sleep(1200);
    await page.screenshot({ path: path.join(outDir, '03-students.png') });
    console.log('Saved 03-students.png');

    // 4. Attendance Records
    console.log('5. Navigating to attendance records...');
    await page.goto('http://127.0.0.1:8080/admin/attendance', { waitUntil: 'domcontentloaded' });
    await sleep(1200);
    await page.screenshot({ path: path.join(outDir, '05-attendance.png') });
    console.log('Saved 05-attendance.png');

    // 5. Face Registration
    console.log('6. Navigating to face registration...');
    await page.goto('http://127.0.0.1:8080/admin/students/1/register-face', { waitUntil: 'domcontentloaded' });
    await sleep(1500);
    await page.screenshot({ path: path.join(outDir, '06-face-register.png') });
    console.log('Saved 06-face-register.png');

    // 6. Kiosk Mode
    console.log('7. Navigating to kiosk...');
    await page.goto('http://127.0.0.1:8080/kiosk', { waitUntil: 'domcontentloaded' });
    await sleep(2000);
    await page.screenshot({ path: path.join(outDir, '04-kiosk.png') });
    console.log('Saved 04-kiosk.png');

    await browser.close();
    console.log('ALL 6 REAL SCREENSHOTS CAPTURED SUCCESSFULLY!');
}

main().catch(err => {
    console.error('Fatal error during screenshots capture:', err);
    process.exit(1);
});

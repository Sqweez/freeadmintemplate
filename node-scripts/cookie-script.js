const puppeteer = require('puppeteer');
const axios = require('axios');

const login = process.argv[2];
const password = process.argv[3];

(async () => {
    // Запускаем браузер
    const browser = await puppeteer.launch({
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });
    const page = await browser.newPage();
    await page.setUserAgent(
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
    );
    await page.setViewport({
        width: 1920, // Ширина экрана для десктопа
        height: 1080, // Высота экрана для десктопа
        deviceScaleFactor: 1, // Отключение масштабирования
        isMobile: false, // Указываем, что это не мобильное устройство
        hasTouch: false, // Указываем, что устройство не поддерживает тач
    });
    // Переходим на указанный адрес
    await page.goto('https://kaspi.kz/merchantcabinet/', {
        waitUntil: 'networkidle2',
    });

    await page.waitForSelector('#email_tab', { timeout: 5000 }); // Ожидание элемента
    await page.click('#email_tab'); // Клик по элементу
    //await page.screenshot({ path: 'screenshot.png' });
    // Вводим email в поле с id="user_email_field"
    await page.type('#user_email_field', login);

    // Нажимаем Enter после ввода email
    await page.keyboard.press('Enter');

    // Ожидаем загрузки поля пароля
    await page.waitForSelector('#password_field');

    // Вводим пароль в поле с id="password_field"
    await page.type('#password_field', password);

    // Нажимаем Enter после ввода пароля
    await page.keyboard.press('Enter');
    // Ожидаем завершения авторизации
    //    await page.waitForSelector('.b-sidebar');
    // await page.screenshot({ path: 'screenshot-2.png' });
    await sleep(2);
    const client = await page.target().createCDPSession();
    const cookies = await client.send('Network.getAllCookies');
    const cookieHeader = cookies.cookies
        .map((cookie) => `${cookie.name}=${cookie.value}`)
        .join('; ');

    console.log(formatCookies(cookieHeader));

    // Закрываем браузер
    await browser.close();
})();

const formatCookies = (cookies) => {
    return cookies.replace(/\n/g, ''); // Убираем символы переноса
};

const sleep = (s) => new Promise((resolve) => setTimeout(resolve, s * 1000));

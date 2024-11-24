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

    // Переходим на указанный адрес
    await page.goto('https://idmc.shop.kaspi.kz/login', {
        waitUntil: 'networkidle2',
    });

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
    await page.waitForSelector('.b-sidebar');
    const currentUrl = page.url();
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

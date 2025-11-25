function loginStart() {
    document.getElementById('captcha').style.display = 'none';
    document.getElementById('loginBtn').style.display = 'none';
    document.getElementById('loginBtn').style.backgroundColor = '#151515';
    document.getElementById('captchaBtn').style.display = '';
}

function captchaOpen() {
    document.getElementById('captcha').style.display = '';
    document.getElementById('loginBtn').style.display = '';
    document.getElementById('captchaBtn').style.display = 'none';
}

function captchaClose() {
    document.getElementById('captcha').style.display = 'none';
    document.getElementById('loginBtn').style.display = 'none';
    document.getElementById('captchaBtn').style.display = '';
}
function requestForm(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);

    xhr.onload = function () {
        if (callback) {
            callback(xhr.response);
        }
    };
    let formData = data ? (data instanceof FormData ? data : new FormData(document.querySelector(data))) : new FormData();
    let csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    if (csrfMetaTag) {
        formData.append('csrf-token', csrfMetaTag.getAttribute("content"));
    }

    xhr.send(formData);
}

function register() {
    const errors_div = document.getElementById('errors');
    errors_div.innerHTML = '';
    requestForm('php/register.php', '#formRegister', function (data) {
        if (data) {
            data = JSON.parse(data);
            if (data instanceof Array) {
                for (let i = 0; i < data.length; i++) {
                    if (data[i] === 0) {
                        errors_div.innerHTML += '<div class="container text-success py-1 px-0">На вашу почту было отправлено письмо с ссылкой для подтверждения регистрации.</div>';
                        document.getElementById('formRegister').reset();
                    }

                    if (data[i] === 1) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Логин должен состоять из латинских букв и цифр, а также быть не длиннее 255</div>';
                    }

                    if (data[i] === 2) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Имя может состоять из латинских букв, кириллицы и пробелов, а также должно быть не длиннее 255</div>';
                    }

                    if (data[i] === 3) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Пароль должен быть не короче 8 и не длиннее 255</div>';
                    }

                    if (data[i] === 4) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Пароли не совпадают</div>';
                    }

                    if (data[i] === 5) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Неправильный формат электронной почты</div>';
                    }

                    if (data[i] === 6) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Указанный домен электронной почты не найден</div>';
                    }

                    if (data[i] === 7) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Не удалось подключение к базе данных. Обратитесь к администратору</div>';
                    }

                    if (data[i] === 8) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Этот логин уже занят</div>';
                    }

                    if (data[i] === 9) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Эта электронная почта уже занята</div>';
                    }

                    if (data[i] === 10) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Ошибка безопасности: csrf-token не задан или не совпадает</div>';
                    }

                    if (data[i] === 11) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Отправка электронного письма не удалась</div>';
                    }

                    if (data[i] === 12) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Вы превысили максимальное количество запросов на регистрацию</div>';
                    }

                    if (data[i] === 13) {
                        errors_div.innerHTML += '<div class="container text-danger py-1 px-0">Ошибка проверки регистрации. Обратитесь к поддержке сайта.</div>';
                    }
                }
            }
        }
    });
}

function login() {
    requestForm("php/login.php", "#formLogin", function (data) {
        if (data === "success") {
            location.pathname = "index.php";
            return;
        }
        document.getElementById("errors").innerHTML = data;
    });
}

const registerbtn = document.querySelector("#registerbtn");
const loginbtn = document.querySelector("#loginbtn");

if (registerbtn) {
    registerbtn.addEventListener("click", register);
}

if (loginbtn) {
    loginbtn.addEventListener("click", login);
}
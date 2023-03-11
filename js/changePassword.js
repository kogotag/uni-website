function requestWithCsrf(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (callback) {
            callback(xhr.response);
        }
    };
    let csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    if (csrfMetaTag) {
        data += "&csrf-token=" + csrfMetaTag.getAttribute("content");
    }

    xhr.send(data ? data : undefined);
}

function findGetParameter(parameterName) {
    var result = null,
            tmp = [];
    location.search
            .substr(1)
            .split("&")
            .forEach(function (item) {
                tmp = item.split("=");
                if (tmp[0] === parameterName)
                    result = decodeURIComponent(tmp[1]);
            });
    return result;
}

const btnChangePassword = document.getElementById("btnChangePassword");
const inputPasswordFirst = document.getElementById("passwordFirst");
const inputPasswordSecond = document.getElementById("passwordSecond");

if (btnChangePassword) {
    btnChangePassword.addEventListener("click", changePassword);
}

function changePassword() {
    passwordFirst = inputPasswordFirst.value;
    passwordSecond = inputPasswordSecond.value;
    requestWithCsrf("php/changePassword.php", "passwordFirst=" + passwordFirst + "&passwordSecond=" + passwordSecond, function(data) {
       if (data === "success") {
           alert("Пароль изменён успешно");
       } else if (typeof data === "string") {
           alert(data);
       } else {
           alert("Ошибка типа возвращаемого значения");
       }
    });
}

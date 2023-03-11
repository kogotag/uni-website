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

const btnResetPassword = document.getElementById("btnResetPassword");

if (btnResetPassword) {
    btnResetPassword.addEventListener("click", resetPassword);
}

function resetPassword() {
    requestForm("php/sendResetPasswordEmail.php", "#formResetPassword", function(data){
        if (data === "success") {
            alert("Инструкции по сбросу пароля отправлены на ваш почтовый ящик");
        } else if (typeof data === "string") {
            alert(data);
        } else {
            alert("Ошибка типа возвращаемого значения");
        }
    });
}
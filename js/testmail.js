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

const btnSendEmail = document.getElementById("btnSendEmail");

if (btnSendEmail) {
    btnSendEmail.addEventListener("click", sendMail);
}

function sendMail() {
    requestForm("php/testMail.php", "#formTestEmail", function(data){
        if (data === "success") {
            alert("Письмо отправлено успешно");
        } else if (typeof data === "string") {
            alert(data);
        } else {
            alert("Ошибка типа возвращаемого значения");
        }
    });
}



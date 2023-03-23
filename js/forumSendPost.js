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

function send() {
    if (!forumTextArea || !tid) {
        return;
    }
    
    requestWithCsrf("/php/forumAddPost.php", "tid=" + tid + "&content=" + forumTextArea.value, function(data) {
        if (data === "success") {
            location.href = "/forum.php?tid=" + tid + "&p=last";
        } else if (typeof data === "string") {
            alert(data);
        } else {
            alert("Ошибка. Сервер вернул неожиданный ответ. Обратитесь к администратору");
        }
    });
}

const forumTextArea = document.getElementById("forumTextArea");
const tid = findGetParameter("tid");

const forumButtonSend = document.getElementById("forumButtonSend");

if (forumButtonSend) {
    forumButtonSend.addEventListener("click", send);
}
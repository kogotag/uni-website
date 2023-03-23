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

function start() {
    reloadPostContent();
}

function reloadPostContent() {
    if (!forumTextArea || !pid) {
        return;
    }

    requestWithCsrf("php/forumGetPostInfo.php", "pid=" + pid, function (data) {
        try {
            data = JSON.parse(data);

            forumTextArea.innerHTML = data["content"];
        } catch (e) {
            if (typeof data === "string") {
                alert(data);
                return;
            } else {
                alert("Ошибка. Сервер вернул неожиданный ответ. Обратитесь к администратору");
                return;
            }
        }
    });
}

function edit() {
    if (!forumTextArea || !pid) {
        return;
    }

    requestWithCsrf("/php/forumEditPost.php", "pid=" + pid + "&content=" + forumTextArea.value, function (data) {
        if (data === "success") {
            if (tid) {
                if (p) {
                    location.href = "/forum.php?tid=" + tid + "&p=" + p;
                } else {
                    location.href = "/forum.php?tid=" + tid + "&p=" + 1;
                }
            } else {
                location.href = "/forum.php";
            }
        } else if (typeof data === "string") {
            alert(data);
        } else {
            alert("Ошибка. Сервер вернул неожиданный ответ. Обратитесь к администратору");
        }
    });
}

const forumTextArea = document.getElementById("forumTextArea");
const pid = findGetParameter("pid");
const tid = findGetParameter("tid");
const p = findGetParameter("p");

const forumButtonSend = document.getElementById("forumButtonSend");

if (forumButtonSend) {
    forumButtonSend.addEventListener("click", edit);
}

start();
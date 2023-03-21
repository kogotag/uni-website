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
    if (!fid && !tid) {
        reloadForums();
    } else if (fid && !tid) {
        reloadTopics();
    } else if (tid && !fid) {
        reloadTopicPage();
    } else {
        alert("ошибка аргументов GET запроса");
    }
}

function forumBackground(counter) {
    if (counter % 2 === 0) {
        return "bg-light";
    } else {
        return "bg-white";
    }
}

function reloadForums() {
    if (!forumBody || !forumHeader) {
        return;
    }
    body = "";
    forumHeader.innerHTML = "Форумы";

    requestWithCsrf("/php/forumGetForums.php", "", function (data) {
        try {
            data = JSON.parse(data);
            if (!Array.isArray(data)) {
                alert("Ошибка. Тип возвращаемого значения не является массивом. Обратитесь к администратору");
                return;
            }
            body += "<div class=\"table-responsive\"><table class=\"table table-bordered\"><thead><tr><th></th><th>Темы</th><th>Обновление</th></tr></thead><tbody>";
            data.forEach(forum => {
                body += "<tr><td><a href=\"/forum.php?fid=" + forum["id"] + "\">" + forum["name"] + "</a><br>" + forum["description"] + "</td><td>" + forum["topics_count"] + "</td><td>" + forum["last_update"] + "</td></tr>";
            });
            body += "</tbody></table></div>";
            forumBody.innerHTML = body;
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

function reloadTopics() {
    if (!forumBody || !forumHeader) {
        return;
    }
    body = "";
    forumHeader.innerHTML = "Темы";

    requestWithCsrf("/php/forumGetTopics.php", "fid=" + fid, function (data) {
        try {
            data = JSON.parse(data);
            if (!Array.isArray(data)) {
                alert("Ошибка. Тип возвращаемого значения не является массивом. Обратитесь к администратору");
                return;
            }
            
            body += "<div class=\"table-responsive\"><table class=\"table table-bordered\"><thead><tr><th></th><th>Автор</th><th>Сообщений</th><th>Обновление</th></tr></thead><tbody>";
            data.forEach(topic => {
               body += "<tr><td><a href=\"/forum.php?tid=" + topic["id"] + "\">" + topic["name"] + "</a></td><td>" + topic["user_name"] + "</td><td>" + topic["msg_count"] + "</td><td>" + topic["last_update"] + "</td></tr>"; 
            });
            body += "</tbody></table></div>";
            forumBody.innerHTML = body;
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

//WIP
function reloadTopicPage() {
    if (!forumBody || !forumHeader) {
        return;
    }
    body = "";
    forumBody.innerHTML = "";
    forumHeader.innerHTML = "";
    
    requestWithCsrf("/php/forumGetTopics.php", "fid=" + fid, function (data) {
        try {
            data = JSON.parse(data);
            if (!Array.isArray(data)) {
                alert("Ошибка. Тип возвращаемого значения не является массивом. Обратитесь к администратору");
                return;
            }
            
            body += "<div class=\"table-responsive\"><table class=\"table table-bordered\"><thead><tr><th></th><th>Автор</th><th>Сообщений</th><th>Обновление</th></tr></thead><tbody>";
            data.forEach(post => {
               body += "";
            });
            body += "</tbody></table></div>";
            forumBody.innerHTML = body;
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

const forumBody = document.getElementById("forumBody");
const forumHeader = document.getElementById("forumHeader");
const fid = findGetParameter("fid");
const tid = findGetParameter("tid");
const p = findGetParameter("p");

start();
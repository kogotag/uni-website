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

function reformatMySqlDate(date) {
    date = new Date(date);
    return date.toLocaleString("ru-RU");
}

function start() {
    reload();
    reloadBreadCrumb();
    reloadPagination();
}

function forumBackground(counter) {
    if (counter % 2 === 0) {
        return "bg-light";
    } else {
        return "bg-white";
    }
}

function reload() {
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

function reloadForums() {
    if (!forumBody || !forumHeader) {
        return;
    }
    let body = "";
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
                body += "<tr><td><a href=\"/forum.php?fid=" + forum["id"] + "\">" + forum["name"] + "</a><br>" + forum["description"] + "</td><td>" + forum["topics_count"] + "</td><td>" + reformatMySqlDate(forum["last_update"]) + "</td></tr>";
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
    let body = "";
    forumHeader.innerHTML = "Темы";

    requestWithCsrf("/php/forumGetTopics.php", "fid=" + fid, function (data) {
        try {
            data = JSON.parse(data);
            if (!Array.isArray(data)) {
                alert("Ошибка. Тип возвращаемого значения не является массивом. Обратитесь к администратору");
                return;
            }

            if (data.length === 0) {
                forumBody.innerHTML = "<p class=\"text-info\">На этом форуме ещё не создано ни одной темы</p>";
                return;
            }

            body += "<div class=\"table-responsive\"><table class=\"table table-bordered\"><thead><tr><th></th><th>Автор</th><th>Сообщений</th><th>Обновление</th></tr></thead><tbody>";
            data.forEach(topic => {
                body += "<tr><td><a href=\"/forum.php?tid=" + topic["id"] + "\">" + topic["name"] + "</a></td><td>" + topic["user_name"] + "</td><td>" + topic["msg_count"] + "</td><td>" + reformatMySqlDate(topic["last_update"]) + "</td></tr>";
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

function reloadTopicPage() {
    if (!forumBody || !forumHeader) {
        return;
    }
    let body = "";
    forumHeader.innerHTML = "";
    if (!p) {
        p = 1;
    }

    requestWithCsrf("/php/forumGetPosts.php", "tid=" + tid + "&p=" + p, function (data) {
        try {
            data = JSON.parse(data);
            if (!Array.isArray(data)) {
                alert("Ошибка. Тип возвращаемого значения не является массивом. Обратитесь к администратору");
                return;
            }

            body += "";
            counter = 0;
            data.forEach(post => {
                edited = "";
                if (parseInt(post["edits_count"]) > 0) {
                    edited = "<small class=\"text-info\">Редактировалось " + post["edits_count"] + " раз. Последний раз " + reformatMySqlDate(post["last_edit_timestamp"]) + "</small>";
                }
                body += "<div class=\"row " + forumBackground(counter) + " py-2\"><div class=\"col-2\"><small class=\"text-info\">" + reformatMySqlDate(post["timestamp"]) + "</small><br><a href=\"\">" + post["user_name"] + "</a></div><div class=\"col-10\">" + post["content"] + "<br><br>" + edited + "</div></div>";
                counter++;
            });
            body += "";
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

    requestWithCsrf("/php/forumGetTopicInfo.php", "tid=" + tid, function (data) {
        try {
            data = JSON.parse(data);

            forumHeader.innerHTML = data["name"];
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

function reloadBreadCrumb() {
    if (!forumBreadCrumb) {
        return;
    }

    if (!fid && !tid) {
        forumBreadCrumbDefault();
    } else if (fid && !tid) {
        forumBreadCrumb.innerHTML = "<li class=\"breadcrumb-item\"><a href=\"/\">Главная</a></li>\n\
<li class=\"breadcrumb-item\"><a href=\"/forum.php\">Форум</a></li>"
        requestWithCsrf("/php/forumGetForumInfo.php", "fid=" + fid, function (data) {
            try {
                data = JSON.parse(data);

                let forumName = data["name"];
                forumBreadCrumb.innerHTML += "<li class=\"breadcrumb-item active\">" + forumName + "</li>";
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
    } else if (tid && !fid) {
        forumBreadCrumb.innerHTML = "<li class=\"breadcrumb-item\"><a href=\"/\">Главная</a></li>\n\
<li class=\"breadcrumb-item\"><a href=\"/forum.php\">Форум</a></li>";
        requestWithCsrf("/php/forumGetTopicBreadCrumb.php", "tid=" + tid, function (data) {
            try {
                data = JSON.parse(data);
                let forumName = data["forum_name"];
                let topicName = data["name"];
                let forumId = data["forum_id"];
                forumBreadCrumb.innerHTML += "<li class=\"breadcrumb-item\"><a href=\"/forum.php?fid=" + forumId + "\">" + forumName + "</a></li>\n\
<li class=\"breadcrumb-item active\">" + topicName + "</li>";
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
    } else {
        forumBreadCrumbDefault();
    }
}

function forumBreadCrumbDefault() {
    if (!forumBreadCrumb) {
        return;
    }
    forumBreadCrumb.innerHTML = "<li class=\"breadcrumb-item\"><a href=\"/\">Главная</a></li>\n\
<li class=\"breadcrumb-item active\">Форум</li>";
}

function reloadPagination() {
    if (!forumPagination) {
        return;
    }

    if (!p) {
        p = 1;
    } else {
        p = parseInt(p);
    }

    if (!fid && tid) {
        requestWithCsrf("/php/forumGetTopicInfo.php", "tid=" + tid, function (data) {
            try {
                data = JSON.parse(data);

                let pagesCount = parseInt(data["pages_count"]);
                let leftBound = Math.max(p - 8, 1);
                let rightBound = Math.min(p + 8, pagesCount);
                let paginationBody = "<nav><ul class=\"pagination\">";

                let prev = "";
                if (p > 1) {
                    prev = "<li class=\"page-item\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=" + (p - 1).toString() + "\">Назад</a></li>";
                }

                let next = "";
                if (p < pagesCount) {
                    next = "<li class=\"page-item\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=" + (p + 1).toString() + "\">Далее</a></li>";
                }

                paginationBody += prev;

                for (let i = leftBound; i <= rightBound; i++) {
                    let active = "";
                    if (i === p) {
                        active = " active";
                    }

                    paginationBody += "<li class=\"page-item" + active + "\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=" + i + "\">" + i + "</a></li>";
                }
                paginationBody += next + "</ul></nav>";
                forumPagination.innerHTML = paginationBody;

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
    } else {
        forumPagination.innerHTML = "";
    }
}

const forumBody = document.getElementById("forumBody");
const forumHeader = document.getElementById("forumHeader");
const forumBreadCrumb = document.getElementById("forumBreadCrumb");
const forumPagination = document.getElementById("forumPagination");
const fid = findGetParameter("fid");
const tid = findGetParameter("tid");
let p = findGetParameter("p");

start();
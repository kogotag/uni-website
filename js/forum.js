class TagParameter {
    name;
    value;
    constructor(name, value) {
        this.name = name;
        this.value = value;
    }

    toString() {
        return "name: "
                + this.name
                + "\nvalue: "
                + this.value;
    }
}

class ImageTag {
    number;
    tag;
    start;
    end;
    params = [];
    width = 100;
    innerText = "";
    center = true;
    constructor(tagString, number, start, end) {
        this.number = number;
        this.tag = tagString;
        this.start = start;
        this.end = end;

        let innerTextPattern = /\[img.*?\](.*?)\[\/img\]/ms;
        let innerTextMatch = tagString.match(innerTextPattern);
        if (innerTextMatch) {
            this.innerText = innerTextMatch[1];
        }

        let paramsPattern = /\[img\s+(?<params>.*?)\]/gms;
        let paramsMatch = tagString.match(paramsPattern);
        let paramsString = undefined;
        if (paramsMatch) {
            paramsString = paramsMatch[0];
        }

        if (paramsString) {
            let separateParameterPattern = /(?<key>\w+)=&quot;(?<val>.*?)\&quot;/gms;
            let separateParameterMatches = paramsString.matchAll(separateParameterPattern);
            if (separateParameterMatches) {
                for (const separateParameterMatch of separateParameterMatches) {
                    this.params.push(new TagParameter(separateParameterMatch.groups.key, separateParameterMatch.groups.val));
                }
            }
        }

        let numberParam = this.params.find(param => param.name === "number");
        if (numberParam) {
            try {
                this.number = parseInt(numberParam.value);
            } catch (e) {
                console.log(e);
            }
        }

        let widthParam = this.params.find(param => param.name === "width");
        if (widthParam) {
            try {
                let widthParamNumber = parseInt(widthParam.value);
                if (widthParamNumber > 0 && widthParamNumber < 100) {
                    this.width = widthParamNumber;
                }
            } catch (e) {
                console.log(e);
            }
        }
        
        let centerParam = this.params.find(param => param.name === "center");
        if (centerParam && centerParam.value === "false") {
            this.center = false;
        }
    }

    toString() {
        return "number: "
                + this.number.toString()
                + "\ntag: "
                + this.tag.toString()
                + "\nstart: "
                + this.start
                + "\nend: "
                + this.end
                + "\nparams: " + this.params.toString();
    }
}

class HeaderTag {
    start;
    end;
    innerText = "";
    tagString;
    params = [];
    center = true;
    constructor(tagString, innerText, paramsString, start, end) {
        this.tagString = tagString;
        this.start = start;
        this.end = end;
        this.innerText = innerText;

        if (paramsString.trim()) {
            let separateParameterPattern = /(?<key>\w+)=&quot;(?<val>.*?)\&quot;/gms;
            let separateParameterMatches = paramsString.matchAll(separateParameterPattern);
            if (separateParameterMatches) {
                for (const separateParameterMatch of separateParameterMatches) {
                    this.params.push(new TagParameter(separateParameterMatch.groups.key, separateParameterMatch.groups.val));
                }
            }
        }

        let centerParam = this.params.find(elem => elem.name === "center");
        if (centerParam && centerParam.value === "false") {
            this.center = false;
        }
    }

    toString() {
        return "tagString: "
                + this.tagString
                + "\ninnerText: "
                + this.innerText
                + "\nstart: "
                + this.start.toString()
                + "\nend: "
                + this.end.toString()
                + "\nparams: "
                + this.params.toString();
    }
}

class ColorTag {
    start;
    end;
    innerText = "";
    tagString;
    params = [];
    colorPalet = {
        "yellow": "#ff0",
        "red": "#f00",
        "blue": "#00f",
        "green": "#0f0",
        "light-blue": "#06f"
    };
    color;
    constructor(tagString, innerText, paramsString, start, end) {
        this.tagString = tagString;
        this.start = start;
        this.end = end;
        this.innerText = innerText;

        if (paramsString.trim()) {
            let separateParameterPattern = /(?<key>\w+)=&quot;(?<val>.*?)\&quot;/gms;
            let separateParameterMatches = paramsString.matchAll(separateParameterPattern);
            if (separateParameterMatches) {
                for (const separateParameterMatch of separateParameterMatches) {
                    this.params.push(new TagParameter(separateParameterMatch.groups.key, separateParameterMatch.groups.val));
                }
            }
        }

        let colorParam = this.params.find(param => param.name === "color");
        if (colorParam && colorParam.value in this.colorPalet) {
            this.color = this.colorPalet[colorParam.value];
        }
    }
}

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

function syncRequest(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, false);
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

function reformatPostText(text, pid) {
    text = reformatImageTags(text, pid);
    text = reformatHeaderTags(text);
    text = reformatColorTags(text);
    text = text.replaceAll("\n", "<br>");

    return text;
}

function reformatImageTags(text, pid) {
    let imgRegExp = /\[img.*?\].*?\[\/img\]/gms;
    let imgMatches = text.matchAll(imgRegExp);
    let imgTags = [];

    if (imgMatches) {
        imgMatches = Array.from(imgMatches);
        let j = 0;
        for (let i = 0; i < imgMatches.length; i++) {
            let imgTag = new ImageTag(imgMatches[i][0], j + 1, imgMatches[i].index, imgMatches[i].index + imgMatches[i][0].length);
            imgTags.push(imgTag);
            if (imgTag.number !== j + 1) {
                j -= 1;
            }
            j++;
        }
    }

    syncRequest("/php/forumGetPostImages.php", "pid=" + pid, function (data) {
        try {
            data = JSON.parse(data);
            if (!Array.isArray(data)) {
                alert("Ошибка. Тип возвращаемого значения не является массивом. Обратитесь к администратору");
                return;
            }

            if (data.length < 1) {
                return;
            }

            if (imgTags.length > 0) {
                let newString = "";
                newString += text.substring(0, imgTags[0].start);
                for (let i = 0; i < imgTags.length; i++) {
                    let image = data.find(elem => parseInt(elem["number"]) === imgTags[i].number);
                    if (image) {
                        let desc = "";
                        if (imgTags[i].innerText) {
                            desc += "<small class=\"text-info\">" + imgTags[i].innerText + "</small>";
                        }
                        
                        let center = "";
                        if (imgTags[i].center) {
                            center = " class=\"d-flex flex-row justify-content-center\"";
                        }
                        
                        newString += "<br><div" + center + "><div class=\"d-flex flex-column align-items-center\" style=\"width: " + imgTags[i].width.toString() + "%\"><img class=\"img-fluid\" src=\"" + image["file_dir"] + image["file_name"] + "\"></img>" + desc + "</div></div>";
                    }
                    if (i < imgTags.length - 1) {
                        newString += text.substring(imgTags[i].end, imgTags[i + 1].start);
                    } else {
                        newString += text.substring(imgTags[i].end, text.length);
                    }
                }
                text = newString;
            }
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

    return text;
}

function reformatHeaderTags(text) {
    let headerTagsPattern = /\[h(?<params>.*?)\](?<text>.*?)\[\/h\]/gms;
    let headerTagsMatches = text.matchAll(headerTagsPattern);
    if (!headerTagsMatches) {
        return text;
    }

    let headerTags = [];

    for (const headerTagMatch of headerTagsMatches) {
        headerTags.push(new HeaderTag(headerTagMatch[0], headerTagMatch.groups.text, headerTagMatch.groups.params, headerTagMatch.index, headerTagMatch.index + headerTagMatch[0].length));
    }

    if (headerTags.length < 1) {
        return text;
    }

    let newText = text.substring(0, headerTags[0].start);
    for (let i = 0; i < headerTags.length; i++) {
        let center = "";
        if (headerTags[i].center) {
            center = " text-center";
        }
        newText += "<h1 class=\"text-break display-4" + center + "\">" + headerTags[i].innerText + "</h1>";
        if (i < headerTags.length - 1) {
            newText += text.substring(headerTags[i].end, headerTags[i + 1].start);
        } else {
            newText += text.substring(headerTags[i].end, text.length);
        }
    }

    return newText;
}

function reformatColorTags(text) {
    let colorTagsPattern = /\[color(?<params>.*?)\](?<text>.*?)\[\/color\]/gms;
    let colorTagsMatches = text.matchAll(colorTagsPattern);
    if (!colorTagsMatches) {
        return text;
    }

    let colorTags = [];

    for (const colorTagsMatch of colorTagsMatches) {
        colorTags.push(new ColorTag(colorTagsMatch[0], colorTagsMatch.groups.text, colorTagsMatch.groups.params, colorTagsMatch.index, colorTagsMatch.index + colorTagsMatch[0].length));
    }

    if (colorTags.length < 1) {
        return text;
    }

    let newText = text.substring(0, colorTags[0].start);
    for (let i = 0; i < colorTags.length; i++) {
        if (colorTags[i].color) {
            newText += "<span style=\"color: " + colorTags[i].color + "\">" + colorTags[i].innerText + "</span>";
        } else {
            newText += colorTags[i].innerText;
        }

        if (i < colorTags.length - 1) {
            newText += text.substring(colorTags[i].end, colorTags[i + 1].start);
        } else {
            newText += text.substring(colorTags[i].end, text.length);
        }
    }

    return newText;
}

function start() {
    specialCases();
    if (stopLoading) {
        return;
    }
    reload();
    reloadBreadCrumb();
    reloadPagination();
    reloadSendButton();
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
                body += "<tr><td><a href=\"/forum.php?tid=" + topic["id"] + "\">" + topic["name"] + "</a></td><td>" + topic["user_name"] + "</td><td>" + topic["posts_count"] + "</td><td>" + reformatMySqlDate(topic["last_update"]) + "</td></tr>";
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

    reloadCreateTopicButton();
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

            counter = 0;
            data.forEach(post => {
                edited = "";
                if (parseInt(post["edits_count"]) > 0) {
                    edited = "<small class=\"text-info\">Редактировалось " + post["edits_count"] + " раз. Последний раз " + reformatMySqlDate(post["last_edit_timestamp"]) + "</small>";
                }

                let editButton = "";
                if (post["owned"]) {
                    editButton = "<br><br><div class=\"d-flex justify-content-end\"><a href=\"/forumEditPost.php?tid=" + tid + "&pid=" + post["id"] + "&p=" + p + "\">Редактировать</a></div>";
                }

                body += "<div class=\"row " + forumBackground(counter) + " py-2\"><div class=\"col-sm-4 col-lg-2\"><small class=\"text-info\">" + reformatMySqlDate(post["timestamp"]) + "</small><br><a href=\"\">" + post["user_name"] + "</a></div><div class=\"col-sm-8 col-lg-10\">" + reformatPostText(post["content"], post["id"]) + "<br>" + edited + editButton + "</div></div>";
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
<li class=\"breadcrumb-item\"><a href=\"/forum.php\">Форум</a></li>";
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

                let first = "";
                let prev = "";
                if (p > 1) {
                    prev = "<li class=\"page-item\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=" + (p - 1).toString() + "\">Назад</a></li>";
                    first = "<li class=\"page-item\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=1\">&lt;&lt;</a></li>";
                }

                let last = "";
                let next = "";
                if (p < pagesCount) {
                    next = "<li class=\"page-item\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=" + (p + 1).toString() + "\">Далее</a></li>";
                    last = "<li class=\"page-item\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=" + pagesCount + "\">&gt;&gt;</a></li>";
                }

                paginationBody += first;
                paginationBody += prev;

                for (let i = leftBound; i <= rightBound; i++) {
                    let active = "";
                    if (i === p) {
                        active = " active";
                    }

                    paginationBody += "<li class=\"page-item" + active + "\"><a class=\"page-link\" href=\"/forum.php?tid=" + tid + "&p=" + i + "\">" + i + "</a></li>";
                }
                paginationBody += next;
                paginationBody += last + "</ul></nav>";
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

function reloadSendButton() {
    if (!forumPlaceForSendButton || !tid || fid) {
        return;
    }

    forumPlaceForSendButton.innerHTML = "<a class=\"btn btn-primary\" href=\"/forumSendPost.php?tid=" + tid + "\">Написать в эту тему</a>";
}

function reloadCreateTopicButton() {
    if (!forumPlaceForSendButton || tid || !fid) {
        return;
    }

    forumPlaceForSendButton.innerHTML = "<a class=\"btn btn-primary\" href=\"/forumCreateTopic.php?fid=" + fid + "\">Создать новую тему</a>";
}

function specialCases() {
    if (!tid) {
        return;
    }

    if (p === "last") {
        stopLoading = true;

        requestWithCsrf("php/forumGetTopicInfo.php", "tid=" + tid, function (data) {
            try {
                data = JSON.parse(data);

                location.href = "/forum.php?tid=" + tid + "&p=" + data["pages_count"];
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

        return;
    }
}

const forumBody = document.getElementById("forumBody");
const forumHeader = document.getElementById("forumHeader");
const forumBreadCrumb = document.getElementById("forumBreadCrumb");
const forumPagination = document.getElementById("forumPagination");
const forumPlaceForSendButton = document.getElementById("forumPlaceForSendButton");
const fid = findGetParameter("fid");
const tid = findGetParameter("tid");
let p = findGetParameter("p");
let rp = findGetParameter("rp");
let stopLoading = false;

start();
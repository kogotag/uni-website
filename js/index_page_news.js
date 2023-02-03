let newsNumber = 0;

const newsDiv = document.getElementById("news");
const sendNewsInfo = document.getElementById("sendNewsInfo");
const newsTextArea = document.getElementById("newsTextArea");
const newsTextAreaHeading = document.getElementById("newsTextAreaHeading");
const btnMoreNews = document.getElementById("moreNews");
const btnSendNews = document.getElementById("sendNews");

if (btnMoreNews) {
    btnMoreNews.addEventListener("click", loadMoreNews);
}

if (btnSendNews) {
    btnSendNews.addEventListener("click", sendNews);
}

function request(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, false);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (callback) {
            callback(xhr.response);
        }
    };

    xhr.send(data ? data : undefined);
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

function loadLastNews() {
    request("/php/getNews.php", "", function (data) {
        try {
            data = JSON.parse(data);
        } catch (e) {
            console.log("scheduleGetContent.php returned not json: " + data);
            return;
        }

        newsNumber = data["id"];

        if (!newsDiv) {
            return;
        }

        newsDiv.innerHTML = data["news"];
    });
}

loadLastNews();

function loadMoreNews() {
    request("/php/getNews.php", "newsNumber=" + newsNumber, function (data) {
        try {
            data = JSON.parse(data);
        } catch (e) {
            console.log("scheduleGetContent.php returned not json: " + data);
            return;
        }

        newsNumber = data["id"];

        if (!newsDiv) {
            return;
        }

        newsDiv.innerHTML += data["news"];
    });
}

function refreshNews() {
    newsNumber = 0;

    if (!newsDiv) {
        return;
    }
    
    newsDiv.innerHTML = "";
    
    loadLastNews();
}

function sendNews() {
    if (sendNewsInfo) {
        sendNewsInfo.innerHTML = "";
    }

    if (!newsTextArea || newsTextArea.value.trim() === "") {
        if (sendNewsInfo) {
            sendNewsInfo.innerHTML = "Пустое сообщение";
        }
    }

    if (!newsTextAreaHeading || newsTextAreaHeading.value.trim() === "") {
        if (sendNewsInfo) {
            sendNewsInfo.innerHTML = "Пустой заголовок";
        }
    }

    //TODO: do it with forms......

    requestWithCsrf("php/sendNews.php", "content=" + newsTextArea.value + "&heading=" + newsTextAreaHeading.value, function (data) {
        if (data === "success") {
            if (sendNewsInfo) {
                sendNewsInfo.innerHTML = "Отправлено успешно";
            }

            refreshNews();
        } else {
            if (sendNewsInfo) {
                sendNewsInfo.innerHTML = data;
            }
        }
    });
}
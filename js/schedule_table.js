let selectedCell;

document.querySelectorAll("#selectableCell").forEach(cell => {
    cell.addEventListener("click", selectCell);
});

const sendCommentButton = document.getElementById("sendComment");

if (sendCommentButton) {
    sendCommentButton.addEventListener("click", sendComment);
}

const sendCommentInfo = document.getElementById("sendCommentInfo");
const commentTextArea = document.getElementById("commentTextArea");
const comments = document.getElementById("comments");

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

const semester = findGetParameter("semester");
const week = findGetParameter("week");

function request(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
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

    xhr.send(data ? data : undefined);
}

function selectCell(event) {
    if (selectedCell) {
        selectedCell.classList.remove("table-success");
    }
    event.target.classList.add("table-success");
    selectedCell = event.target;

    let day = selectedCell.classList[0];
    let number = selectedCell.classList[1];

    if (!semester || !week || !day || !number) {
        console.log("vseploha");
        return;
    }

    day = day.substring(3);
    number = number.substring(6);

    request("/php/getComments.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
        document.getElementById("comments").innerHTML = data;
    });

    request("/php/getMedia.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
        document.getElementById("media").innerHTML = data;
    });

    request("/php/getAudios.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
        document.getElementById("media").innerHTML += data;
    });
}

function sendComment() {
    if (!selectedCell) {
        if (sendCommentInfo) {
            sendCommentInfo.innerHTML = "Выберите ячейку";
        }
        return;
    } else {
        sendCommentInfo.innerHTML = "";
    }

    if (!commentTextArea || commentTextArea.value.trim() === "") {
        if (sendCommentInfo) {
            sendCommentInfo.innerHTML = "Пустое сообщение";
        }
        return;
    } else {
        sendCommentInfo.innerHTML = "";
    }

    let day = selectedCell.classList[0];
    let number = selectedCell.classList[1];

    if (!semester || !week || !day || !number) {
        console.log("vseploha");
        return;
    }

    day = day.substring(3);
    number = number.substring(6);

    requestWithCsrf("php/sendComment.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number + "&content=" + commentTextArea.value, function (data) {
        if (data === "success") {
            if (sendCommentInfo) {
                sendCommentInfo.innerHTML = "Сообщение отправлено";
            }

            request("/php/getComments.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
                document.getElementById("comments").innerHTML = data;
            });
        } else {
            if (sendCommentInfo) {
                sendCommentInfo.innerHTML = data;
            }
        }
    });
}
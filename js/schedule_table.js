let selectedCell;
document.querySelectorAll("#selectableCell").forEach(cell => {
    cell.addEventListener("click", selectCell);
});

const sendCommentButton = document.getElementById("sendComment");

if (sendCommentButton) {
    sendCommentButton.addEventListener("click", sendComment);
}

const sendAudioButton = document.getElementById("sendAudio");

if (sendAudioButton) {
    sendAudioButton.addEventListener("click", sendAudio);
}

const sendVideoButton = document.getElementById("sendVideo");

if (sendVideoButton) {
    sendVideoButton.addEventListener("click", sendVideo);
}

const sendCommentInfo = document.getElementById("sendCommentInfo");
const commentTextArea = document.getElementById("commentTextArea");
const comments = document.getElementById("comments");
const sendMediaInfo = document.getElementById("sendMediaInfo");
const audioInput = document.getElementById("audioInput");
const audioModal = document.getElementById("audioModal");
const uploadBar = document.getElementById("uploadBar");
const videoUrlInput = document.getElementById("videoUrlInput");

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
    let csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    if (csrfMetaTag) {
        data += "&csrf-token=" + csrfMetaTag.getAttribute("content");
    }

    xhr.send(data ? data : undefined);
}

function requestForm(url, data, onload_callback, onprogress_callback = undefined) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);

    xhr.onload = function () {
        if (onload_callback) {
            onload_callback(xhr.response);
        }
    };

    xhr.upload.addEventListener("progress", function (event) {
        if (onprogress_callback) {
            onprogress_callback(event);
        }
    });

    let formData = data ? (data instanceof FormData ? data : new FormData(document.querySelector(data))) : new FormData();
    let csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
    if (csrfMetaTag) {
        formData.append('csrf-token', csrfMetaTag.getAttribute("content"));
    }

    xhr.send(formData);
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

function sendAudio() {
    if (!selectedCell) {
        if (sendMediaInfo) {
            sendMediaInfo.innerHTML = "Выберите ячейку";
        }
        return;
    } else {
        sendMediaInfo.innerHTML = "";
    }

    if (!audioInput || audioInput.files.length < 1) {
        if (sendMediaInfo) {
            sendMediaInfo.innerHTML = "Выберите файл";
        }
        return;
    } else {
        sendMediaInfo.innerHTML = "";
    }

    let day = selectedCell.classList[0];
    let number = selectedCell.classList[1];

    if (!semester || !week || !day || !number) {
        console.log("vseploha");
        return;
    }

    day = day.substring(3);
    number = number.substring(6);

    let data = new FormData();
    data.append('semester', semester);
    data.append("week", week);
    data.append("day", day);
    data.append("number", number);
    data.append("content", audioInput.files[0]);

    requestForm("php/sendAudio.php", data, function (data) {
        if (data === "success") {
            if (sendMediaInfo) {
                sendMediaInfo.innerHTML = "Аудио отправлено";
            }

            request("/php/getMedia.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
                document.getElementById("media").innerHTML = data;
            });

            request("/php/getAudios.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
                document.getElementById("media").innerHTML += data;
            });
        } else {
            if (sendMediaInfo) {
                sendMediaInfo.innerHTML = data;
            }
        }
    }, function (progressEvent) {
        if (progressEvent.lengthComputable) {
            const percentCompleted = Math.floor(progressEvent.loaded / progressEvent.total * 100);
            if (uploadBar) {
                uploadBar.setAttribute("value", percentCompleted);
            }
        }
    });
}

function sendVideo() {
    if (!selectedCell) {
        if (sendMediaInfo) {
            sendMediaInfo.innerHTML = "Выберите ячейку";
        }
        return;
    } else {
        sendMediaInfo.innerHTML = "";
    }

    if (!videoUrlInput || videoUrlInput.value.trim() === "") {
        if (sendMediaInfo) {
            sendMediaInfo.innerHTML = "Введите ссылку на видео";
        }
        return;
    } else {
        sendMediaInfo.innerHTML = "";
    }

    let day = selectedCell.classList[0];
    let number = selectedCell.classList[1];

    if (!semester || !week || !day || !number) {
        console.log("vseploha");
        return;
    }

    day = day.substring(3);
    number = number.substring(6);

    requestWithCsrf("php/sendVideo.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number + "&content=" + videoUrlInput.value, function (data) {
        if (data === "success") {
            if (sendMediaInfo) {
                sendMediaInfo.innerHTML = "Ссылка добавлена";
            }

            request("/php/getMedia.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
                document.getElementById("media").innerHTML = data;
            });

            request("/php/getAudios.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
                document.getElementById("media").innerHTML += data;
            });
        } else {
            if (sendMediaInfo) {
                sendMediaInfo.innerHTML = data;
            }
        }
    });
}
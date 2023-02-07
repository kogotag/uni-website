let selectedCell;
let selectedSubject;
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

const changeDescButton = document.getElementById("changeDescButton");

if (changeDescButton) {
    changeDescButton.addEventListener("click", changeDesc);
}

const sendAttachmentButton = document.getElementById("sendAttachmentButton");

if (sendAttachmentButton) {
    sendAttachmentButton.addEventListener("click", sendAttachment);
}

const deleteSubjectButton = document.getElementById("deleteSubjectButton");

if (deleteSubjectButton) {
    deleteSubjectButton.addEventListener("click", deleteSubject);
}

const addSubjectButton = document.getElementById("addSubjectButton");

if (addSubjectButton) {
    addSubjectButton.addEventListener("click", addSubject);
}

const sendCommentInfo = document.getElementById("sendCommentInfo");
const commentTextArea = document.getElementById("commentTextArea");
const comments = document.getElementById("comments");
const sendMediaInfo = document.getElementById("sendMediaInfo");
const audioInput = document.getElementById("audioInput");
const attachmentInput = document.getElementById("attachmentInput");
const audioModal = document.getElementById("audioModal");
const uploadBar = document.getElementById("uploadBar");
const uploadBarAttachment = document.getElementById("uploadBarAttachment");
const videoUrlInput = document.getElementById("videoUrlInput");
const changeDescInfo = document.getElementById("changeDescInfo");
const hwFromThisDayInput = document.getElementById("hwFromThisDayInput");
const descModal = document.getElementById("descModal");
const addSubjectDropdown = document.getElementById("addSubjectDropdown");
const inputAddSubjectLecturer = document.getElementById("inputAddSubjectLecturer");
const inputAddSubjectRoom = document.getElementById("inputAddSubjectRoom");

function isJsonObject(strData) {
    try {
        JSON.parse(strData);
    } catch (e) {
        return false;
    }
    return true;
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

const semester = findGetParameter("semester");
const week = findGetParameter("week");
fillAddSubjectDropdown();

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

function updateScheduleContent(semester, week, day, number) {
    request("/php/scheduleGetContent.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
        try {
            data = JSON.parse(data);
            document.getElementById("description").innerHTML = data["desc"];
            document.getElementById("comments").innerHTML = data["comments"];
            document.getElementById("media").innerHTML = data["videos"] + data["audios"] + data["attachments"];
        } catch (e) {
            console.log("scheduleGetContent.php returned not json: " + data);
        }
    });
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

    updateScheduleContent(semester, week, day, number);
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

            updateScheduleContent(semester, week, day, number);
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

            updateScheduleContent(semester, week, day, number);
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

            updateScheduleContent(semester, week, day, number);
        } else {
            if (sendMediaInfo) {
                sendMediaInfo.innerHTML = data;
            }
        }
    });
}

function changeDesc() {
    if (!selectedCell) {
        if (changeDescInfo) {
            changeDescInfo.innerHTML = "Выберите ячейку";
        }
        return;
    } else {
        changeDescInfo.innerHTML = "";
    }

    if (!descModal) {
        return;
    }

    let day = selectedCell.classList[0];
    let number = selectedCell.classList[1];

    if (!semester || !week || !day || !number) {
        console.log("vseploha");
        return;
    }

    day = day.substring(3);
    number = number.substring(6);

    requestWithCsrf("php/changeDesc.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number + "&hwFrom=" + hwFromThisDayInput.value, function (data) {
        if (data === "success") {
            if (changeDescInfo) {
                changeDescInfo.innerHTML = "Описание изменено";
            }

            updateScheduleContent(semester, week, day, number);
        } else {
            if (changeDescInfo) {
                changeDescInfo.innerHTML = data;
            }
        }
    });
}

function sendAttachment() {
    if (!selectedCell) {
        if (sendMediaInfo) {
            sendMediaInfo.innerHTML = "Выберите ячейку";
        }
        return;
    } else {
        sendMediaInfo.innerHTML = "";
    }

    if (!attachmentInput || attachmentInput.files.length < 1) {
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
    data.append("content", attachmentInput.files[0]);

    requestForm("php/sendAttachment.php", data, function (data) {
        if (data === "success") {
            if (sendMediaInfo) {
                sendMediaInfo.innerHTML = "Вложение отправлено";
            }

            updateScheduleContent(semester, week, day, number);
        } else {
            if (sendMediaInfo) {
                sendMediaInfo.innerHTML = data;
            }
        }
    }, function (progressEvent) {
        if (progressEvent.lengthComputable) {
            const percentCompleted = Math.floor(progressEvent.loaded / progressEvent.total * 100);
            if (uploadBarAttachment) {
                uploadBarAttachment.setAttribute("value", percentCompleted);
            }
        }
    });
}

function deleteSubject() {
    if (!selectedCell) {
        alert("Выберите ячейку");
        return;
    }

    let day = selectedCell.classList[0];
    let number = selectedCell.classList[1];

    if (!semester || !week || !day || !number) {
        alert("Ошибка: координаты ячейки расписания не определены");
        return;
    }

    //TODO: move it to separate function
    day = day.substring(3);
    number = number.substring(6);

    requestWithCsrf("php/deleteSubject.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number, function (data) {
        if (data === "success") {
            alert("Предмет успешно был удалён из расписания. Обновите страницу");
        } else {
            alert("Возникли следующие ошибки:\n" + data);
        }
    });
}

function fillAddSubjectDropdown() {
    if (!semester || !week) {
        return;
    }

    requestWithCsrf("php/getSemesterSubjects.php", "semester=" + semester + "&week=" + week, function (data) {
        if (isJsonObject(data)) {
            let subjectList = JSON.parse(data);
            if (!Array.isArray(subjectList)) {
                alert("Ошибка. Возвращаемое значение из getSemesterSubjects.php не является массивом");
                return;
            }

            subjectList.forEach(function (subject) {
                addSubjectDropdown.innerHTML += "<button class=\"dropdown-item subject" + subject["id"] + "\" id=\"subjectDropdownItem\" type=\"button\">" + subject["aliasName"] + "</button>\n";
            });
            document.querySelectorAll("#subjectDropdownItem").forEach(item => {
                item.addEventListener("click", selectSubjectDropdownItem);
            });

        } else if (typeof data === "string") {
            alert(data);
        } else {
            alert("Ошибка. Тип возвращаемого значения из getSemesterSubjects.php не установлен");
        }
    });
}

function selectSubjectDropdownItem() {
    if (selectedSubject) {
        selectedSubject.classList.remove("active");
    }
    
    event.target.classList.add("active");
    selectedSubject = event.target;
}

function addSubject() {
    if (!selectedSubject) {
        alert("Сначала выберите предмет, который вы хотите добавить");
        return;
    }
    
    if (!selectedCell) {
        alert("Выберите ячейку в таблице с расписанием");
        return;
    }
    
    let subject_id = selectedSubject.classList[1];

    if (!subject_id) {
        return;
    }

    subject_id = subject_id.substring(("subject").length);
    
    let day = selectedCell.classList[0];
    let number = selectedCell.classList[1];

    if (!semester || !week || !day || !number) {
        console.log("vseploha");
        return;
    }

    day = day.substring(3);
    number = number.substring(6);
    
    if (inputAddSubjectLecturer.value.trim() === "") {
        alert("Введите имя лектора");
        return;
    }
    
    if (inputAddSubjectRoom.value.trim() === "") {
        alert("Введите название аудитории");
        return;
    }
    
    requestWithCsrf("php/addSubject.php", "semester=" + semester + "&week=" + week + "&day=" + day + "&number=" + number + "&subject_id=" + subject_id + "&lecturer=" + inputAddSubjectLecturer.value + "&room=" + inputAddSubjectRoom.value, function(data){
        if (data === "success") {
            alert("Предмет был добавлен. Обновите страницу");
        } else {
            alert("Возникли следующие ошибки:\n" + data);
        }
    });
}
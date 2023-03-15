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

function progressBar(progress, value) {
    if (!progress || typeof value !== "number") {
        return;
    }

    if (value === 0) {
        progress.innerHTML = "";
    } else {
        progress.innerHTML = value.toString() + "%";
    }
    progress.setAttribute("style", "width: " + value.toString() + "%");
}

function send() {
    if (!forumTextArea || !tid) {
        return;
    }

    let form = new FormData();
    form.append("tid", tid);
    form.append("content", forumTextArea.value);
    form.append("images", JSON.stringify(images));

    requestForm("/php/forumAddPost.php", form, function (data) {
        if (data === "success") {
            location.href = "/forum.php?tid=" + tid + "&p=last";
        } else if (typeof data === "string") {
            alert(data);
        } else {
            alert("Ошибка. Сервер вернул неожиданный ответ. Обратитесь к администратору");
        }
    });
}

function postAddImage() {
    //TODO выводить, что надо выбрать файл
    if (!forumPostMessagesPreview || !forumInputImage || forumInputImage.files.length < 1) {
        return;
    }

    let form = new FormData();
    form.append("file", forumInputImage.files[0]);

    requestForm("/php/forumPostAddImage.php", form, function (data) {
        try {
            data = JSON.parse(data);

            forumPostMessagesPreview.innerHTML += "<div class=\"position-relative mr-2 mb-2\" id=\"forumImage\"><img class=\"img-thumbnail\" src=\"" + data["file_name"] + "\" width=\"200\"></img><button type=\"button\" class=\"position-absolute close\" style=\"top: 0; right: 0;\" onclick=\"deleteImage(this)\"><span style=\"color: rgba(255, 0, 0, 1);\" aria-hidden=\"true\">&times;</span></button></div>";
            images.push(data["id"]);
        } catch (e) {
            if (typeof data === "string") {
                alert(data);
                return;
            } else {
                alert("Ошибка. Сервер вернул неожиданный ответ. Обратитесь к администратору");
                return;
            }
        }
    }, function (progressEvent) {
        if (progressEvent.lengthComputable) {
            const percentCompleted = Math.floor(progressEvent.loaded / progressEvent.total * 100);
            progressBar(forumProgressImage, percentCompleted);
        }
    });
}

function deleteImage(element) {
    let parent = element.parentElement;
    if (!parent) {
        return;
    }

    let imagesBlocks = Array.from(document.querySelectorAll("#forumImage"));
    let number = imagesBlocks.indexOf(parent);

    images.splice(number, 1);
    parent.remove();
}

let images = [];

const forumTextArea = document.getElementById("forumTextArea");
const forumProgressImage = document.getElementById("forumProgressImage");
const forumPostMessagesPreview = document.getElementById("forumPostMessagesPreview");
const forumInputImage = document.getElementById("forumInputImage");
const tid = findGetParameter("tid");

const forumButtonSend = document.getElementById("forumButtonSend");

if (forumButtonSend) {
    forumButtonSend.addEventListener("click", send);
}

const forumButtonAddImage = document.getElementById("forumButtonAddImage");

if (forumButtonAddImage) {
    forumButtonAddImage.addEventListener("click", postAddImage);
}
const buttonGitPull = document.getElementById("buttonGitPull");

if (buttonGitPull) {
    buttonGitPull.addEventListener("click", gitPull);
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

function gitPull() {
    requestWithCsrf("php/bringRemoteChanges.php", "?", function(data) {
       if (data === "success") {
           alert("Удалённые изменения успешно применены");
       } else {
           alert(data);
       }
    });
}


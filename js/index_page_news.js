let newsNumber = 0;

const newsDiv = document.getElementById("news");
const btnMoreNews = document.getElementById("moreNews");

if (btnMoreNews) {
    btnMoreNews.addEventListener("click", loadMoreNews);
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

function loadLastNews() {
    request("/php/getNews.php", "", function(data) {
        try {
            data = JSON.parse(data);
        } catch (e) {
            console.log("scheduleGetContent.php returned not json: " + data);
            return;
        }
        
        newsNumber = data["id"];
        
        if (!newsDiv){
            return;
        }
        
        newsDiv.innerHTML = data["news"];
    });
}

loadLastNews();

function loadMoreNews() {
    request("/php/getNews.php", "newsNumber=" + newsNumber, function(data) {
        try {
            data = JSON.parse(data);
        } catch (e) {
            console.log("scheduleGetContent.php returned not json: " + data);
            return;
        }
        
        newsNumber = data["id"];
        
        if (!newsDiv){
            return;
        }
        
        newsDiv.innerHTML += data["news"];
    });
}
function mouseOver(row) {
    row.style.backgroundColor = "#E18728";
    row.style.color = "black";
}

function mouseOut(row) {
    if (row.rowIndex % 2) {
        row.style.backgroundColor = "lightgray";
        row.style.color = "gray";
    } else {
        row.style.backgroundColor = "gray";
        row.style.color = "white";
    }
}

function loadXMLDoc(glumacId, imgUrl, pageUrl) {

    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "detalji.php" + "?" + "glumacId=" + glumacId + "&imgUrl=" + imgUrl
        + "&pageUrl=" + pageUrl, true);

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
            document.getElementById("glumciPodaci").innerHTML = xhttp.responseText;
        }
    };
    xhttp.send();
}


function loadXMLDocPlace(glumacId, imgUrl, pageUrl, summary, nomCoordsLat, nomCoordsLon, restCoordsLat, restCoordsLon) {

    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "detalji.php" + "?" + "glumacId=" + glumacId + "&imgUrl=" + imgUrl
        + "&pageUrl=" + pageUrl + "&summary=" + summary
        + "&nomCoordsLat=" + nomCoordsLat + "&nomCoordsLon=" + nomCoordsLon
        + "&restCoordsLat=" + restCoordsLat + "&restCoordsLon=" + restCoordsLon, true);

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
            document.getElementById("glumciPodaci").innerHTML = xhttp.responseText;
        }
    };
    xhttp.send();
}


function reloadPrevPage(pageUrl) {
    var xhttp = new XMLHttpRequest();

    xhttp.open("GET", pageUrl, true);
    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
            document.getElementById("searchPageResults").innerHTML = xhttp.responseText;
        }
    };
    xhttp.send();
}

function loading() {
    //code found here: https://codepen.io/TimLamber/pen/wBMmYq

    var twoToneButton = document.querySelector('.lessButton');

    twoToneButton.innerHTML = "Manje";
    twoToneButton.classList.add('spinning');

    setTimeout(
        function () {
            twoToneButton.classList.remove('spinning');
            twoToneButton.innerHTML = "Manje";

        }, 20000);
}

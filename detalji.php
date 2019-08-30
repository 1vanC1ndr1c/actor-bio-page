<?php $glumacId = isset($_GET['glumacId']) ? $_GET['glumacId'] : 'no'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="dizajn.css">
    <title>Rezultat Pretrage</title>
    <script type="text/javascript" src="detalji.js"></script>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css"/>
    <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <?php if ($glumacId === "99") {
        echo
        "<style>
         #map {
            width: 980px;
            height: 250px;
            display: block;
            position: relative;
        }
        </style>";
    } ?>
</head>

<body>

<?php
require('funkcije.php');

$dom = new DOMDocument();
$dom->load('podaci.xml');
$xp = new DOMXPath($dom);

//get the actor
$glumac = $xp->query("/podaci/glumac[@glumacId=$glumacId]")->item(0);


//get the actor elements
$imgUrl = isset($_GET['imgUrl']) ? $_GET['imgUrl'] : 'no';
$summary = isset($_GET['summary']) ? $_GET['summary'] : 'no';
if ($glumacId != "99") {
    $glumacIme = $glumac->getElementsByTagName("ime")->item(0)->nodeValue;
    $glumacPrezime = $glumac->getElementsByTagName("prezime")->item(0)->nodeValue;
    $glumacDatumRodjenja = $glumac->getElementsByTagName("datum_rodjenja")->item(0)->nodeValue;
    $filmografija = $glumac->getElementsByTagName("filmografija")->item(0)->nodeValue;
    $zanimanja = $glumac->getElementsByTagName("zanimanje");
    $summary = $glumac->getElementsByTagName("biografija")->item(0)->nodeValue;;
    if (strlen($summary) > 1400) {
        $summary = substr($summary, 0, strrpos($summary, ". ", 1400 - strlen($summary)) + 1);
    }
}

if ($glumacId === "99") {
    $glumacPrezime = "";
    $glumacIme = "Philadelphia Museum of Art";
}
echo "<div class=\"formName\">$glumacIme $glumacPrezime </div>";

echo "<div class=\"imageRow\">";
echo '<img class ="actorImageMorePage" src="' . $imgUrl . '" alt= "glumac slika"/>';
echo "<span class=\"emptyPadding\"></span>";
echo "<span class = \"morePageText\">" . $summary . "</span>";
echo "</div>";

if ($glumacId != "99") {
    echo "<div class=\"morePageTitles\">" . "Nacionalnost:";
    echo "<span class = \"morePageText\">" . "  " . $glumac->getAttribute("nacionalnost") . "</span>";
    echo "</div>";

    echo "<div class=\"morePageTitles\">" . "Spol:";
    echo "<span class = \"morePageText\">" . "  ";
    if ($glumac->getAttribute('glumacSpol') == "M") echo "Muško";
    else echo "Žensko";
    echo "</span>";
    echo "</div>";

    echo "<div class=\"morePageTitles\">" . "Datum Rođenja:";
    echo "<span class = \"morePageText\">" . "  " . $glumacDatumRodjenja . "</span>";
    echo "</div>";

    echo "<div class=\"morePageTitles\">" . " Zanimanja:";
    echo "<span class = \"morePageText\">" . "  ";
    for ($i = 0; $i < $zanimanja->length; $i++) {
        echo $zanimanja->item($i)->getAttribute("imeZanimanja");
        if ($i < $zanimanja->length - 1) {
            echo ", ";
        }
    }
    echo "</span>";
    echo "</div>";

    echo "<div class=\"morePageTitles\">" . "Broj Djece:";
    echo "<span class = \"morePageText\">" . "  " . $glumac->getAttribute('brojDjece') . "</span>";
    echo "</div>";

    echo "<div class=\"morePageTitles\">" . "Filmografija:" . "</div>";
    echo $filmografija;

    echo "<div class=\"morePageTitles\">" . "Filmografija(API):" . "</div>";
    echo getFilmography($glumacIme . " " . $glumacPrezime);
} else {
    echo "<div class=\"morePageTitles\">" . "Filmografija:" . "</div>";
    echo "Rocky, Rocky II, Rocky 3, Creed";

    $nomCoordsLat = isset($_GET['nomCoordsLat']) ? $_GET['nomCoordsLat'] : 'no';
    $nomCoordsLon = isset($_GET['nomCoordsLon']) ? $_GET['nomCoordsLon'] : 'no';
    $restCoordsLat = isset($_GET['restCoordsLat']) ? $_GET['restCoordsLat'] : 'no';
    $restCoordsLon = isset($_GET['restCoordsLon']) ? $_GET['restCoordsLon'] : 'no';

    echo "<div class=\"morePageTitles\">" . "Nominatin Coordinates:" . "</div>";
    echo "<div>";
    echo $nomCoordsLat;
    echo "</div>";
    echo "<div>";
    echo $nomCoordsLon;
    echo "</div>";

    echo "<div class=\"morePageTitles\">" . "Rest Coordinates:" . "</div>";
    echo "<div>";
    echo $restCoordsLat;
    echo "</div>";
    echo "<div>";
    echo $restCoordsLon;
    echo "</div>";

}

//"less" button ======================================================================================================
$pageUrl = isset($_GET['pageUrl']) ? $_GET['pageUrl'] : 'no';
$pageUrl = str_replace('AND', '&', $pageUrl);
$pageUrl = str_replace('EQ', '=', $pageUrl);
$pageUrl = str_replace('localhost/', '', $pageUrl);

?>

<div class="lessButtonContainer">
    <button class="lessButton" value="Manje " onclick="reloadPrevPage('<?php $pageUrl ?>');loading()">Manje</button>
</div>

</body>
</html>
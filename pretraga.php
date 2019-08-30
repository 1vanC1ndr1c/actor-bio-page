<?php
require('funkcije.php');

$dom = new DOMDocument();
$dom->load('podaci.xml');

$xp = new DOMXPath($dom);

$filter = handleReq();
$podaci = $xp->query($filter);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="dizajn.css">
    <title>Rezultat Pretrage</title>
    <script type="text/javascript" src="detalji.js"></script>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css"/>
    <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
    <style>
        #map {
            color: black;
            width: 980px;
            height: 250px;
            display: none;
        }
    </style>
</head>

<body id="searchPageResults">

<header>
    <a class="headerImageHomePageLink" href="index.html">
        <img class="headerImage" src="resources/images/don_vito.png" alt="godfather_image"/>
        <img class="headerImage" src="resources/images/homeBox.png" alt="house_icon"/>
    </a>
    <span class="emptyPadding"></span>
    <div class="pageTitle">Biografije Glumaca</div>
</header>

<section class="middleSection">
    <nav>
        <div class="navigationTitle">Poveznice</div>

        <div class="navButtonsWrapper">
            <input class="navButton" type="button" value="Početna Stranica"
                   onclick="window.location.href='index.html';"/>

            <input class="navButton" type="button" value="Pretraživanje"
                   onclick="window.location.href='obrazac.html';"/>

            <input class="navButton" type="button" value="Podaci"
                   onclick="window.location.href='podaci.xml'"/>

            <input class="navButton" type="button" value="Kolegij OR"
                   onclick="window.location.href='http://www.fer.unizg.hr/predmet/or';"/>

            <input class="navButton" type="button" value="Sjedište FER-a"
                   onclick="window.open('http://www.fer.unizg.hr')"/>

            <input class="navButton" type="button" value="E-mail autora"
                   onclick="window.location.href='mailto:cindric95@gmail.com'"/>
        </div>
    </nav>


    <div class="indexPageText">
        <div id="map"></div>
        <table id="glumciPodaci">
            <tr>
                <th>Slika</th>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Koordinate(REST)</th>
                <th>Koordinate(Nominatim)</th>
                <th>Filmografija</th>
                <th>Sažetak</th>
                <th>Više informacija</th>
            </tr>
            <?php
            foreach ($podaci as $glumac) {

                $actorWikiPageTitle = $glumac->getAttribute('wikiPageTitle');
                $actorJsonFile = getJson($actorWikiPageTitle);
                $glumacId = $glumac->getAttribute('glumacId');
                //data from API
                $imgUrl = getImgUrl($actorJsonFile);
                $restCoordinates = getRestCoordinates($actorJsonFile);
                $glumacNominatimKoordinate = getMediaWikiCoordinates($glumac->getAttribute('wikiPageTitle'));
                $pageUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $pageUrl = str_replace('&', 'AND', $pageUrl);
                $pageUrl = str_replace('=', 'EQ', $pageUrl);
                $summary = getSummary($actorJsonFile);

                echo "<tr onmouseover=\"mouseOver(this)\" onmouseout=\"mouseOut(this)\"><td>";
                echo '<img class ="actorImage" src="' . $imgUrl . '" alt= "glumac slika"/>';

                echo "</td><td>";
                echo $glumac->getElementsByTagName('ime')->item(0)->nodeValue;

                echo "</td><td>";
                echo $glumac->getElementsByTagName('prezime')->item(0)->nodeValue;

                echo "</td><td>";
                echo "Lat: " . $restCoordinates['lat'] . "</br>" . "Lon: " . $restCoordinates['lon'];

                echo "</td><td>";

                echo "Lat: " . $glumacNominatimKoordinate[0] . "</br>" . "Lon: " . $glumacNominatimKoordinate[1];
                echo "</td><td>";

                echo $glumac->getElementsByTagName('filmografija')->item(0)->nodeValue;
                echo "</td><td>";

                echo $summary;
                echo "</td><td>";

                echo "<input class=\"moreButton\" type=\"button\" value=\"Više\"  data-style=\"expand-left\"
                                                       onclick=\"loadXMLDoc('$glumacId', '$imgUrl', '$pageUrl' )\">";
                echo "</td></tr>";
            }
            ?>
            <tr>
                <td>
                    <?php
                    $pageUrl = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    $pageUrl = str_replace('&', 'AND', $pageUrl);
                    $pageUrl = str_replace('=', 'EQ', $pageUrl);

                    $actorWikiPageTitle = "Philadelphia_Museum_of_Art";
                    $actorJsonFile = getJson($actorWikiPageTitle);
                    $imgUrl = getImgUrl($actorJsonFile);
                    $summary = getSummary($actorJsonFile);
                    echo '<img src="' . $imgUrl . '" alt= "produkcijska kuca slika" width = "100" />';
                    ?></td>

                <td><?php echo "Philadelphia Museum of Art"; ?></td>
                <td><?php echo "/"; ?></td>
                <td><?php
                    $restCoordinates = getRestCoordinates($actorJsonFile);
                    echo "Lat: " . $restCoordinates['lat'] . "</br>" . "Lon: " . $restCoordinates['lon'];
                    ?></td>
                <td><?php
                    $nominatimKoordinate = getNominatimCoordinates("Philadelphia_Museum_of_Art");
                    echo "Lat: " . $nominatimKoordinate[0] . "</br>" . "Lon: " . $nominatimKoordinate[1];
                    ?></td>
                <td><?php echo "Rocky, Rocky II, Rocky 3, Creed"; ?></td>
                <td><?php echo getSummary($actorJsonFile); ?>
                </td>
                <td><?php
                    $glumacId = "99";
                    $restCoordinatesLat = $restCoordinates['lat'];
                    $restCoordinatesLon = $restCoordinates['lon'];
                    echo "<input class=\"moreButton\" type=\"button\" value=\"Više\"  data-style=\"expand-left\"
                                                                             onclick=\"loadXMLDocPlace('99',
                                                                              '$imgUrl', '$pageUrl', '$summary'
                                                                             , '$nominatimKoordinate[0]', '$nominatimKoordinate[1]',
                                                                             '$restCoordinatesLat','$restCoordinatesLon' ); loadMap()\">";
                    ?>
                </td>
            </tr>
        </table>
    </div>
</section>
<footer>
    Ivan Cindrić, 2019
</footer>
</body>
<script>
    function loadMap() {
        var restLat = <?php echo $restCoordinatesLat;?>;
        var restLon = <?php echo $restCoordinatesLon;?>;
        var map = L.map('map', {
            center: [restLat, restLon],
            zoom: 16
        });

        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        var restMarker = L.marker([restLat, restLon]).bindPopup("Rest Coordinates").addTo(map);
        var nomLat = <?php echo $nominatimKoordinate[0];?>;
        var nomLon = <?php echo $nominatimKoordinate[1];?>;
        var restMarker = L.marker([nomLat, nomLon]).bindPopup("Nominatim Coordinates").addTo(map);

        const coordinates = [[restLat, restLon], [nomLat, nomLon]];
        const configObject = {color: 'red'};
        var polyline = L.polyline(coordinates, configObject).addTo(map);

        setTimeout(function () {
            map.invalidateSize();
        }, 0);


    }
</script>
</html>
<?php

function handleReq()
{
    $filter = array();

    if (!empty($_REQUEST['imeGlumca'])) {
        array_push($filter, 'contains(' . lowerCase('ime') . ', "' . lowerCase2($_REQUEST['imeGlumca']) . '")');
    }
    if (!empty($_REQUEST['prezimeGlumca'])) {
        array_push($filter, 'contains(' . lowerCase('prezime') . ', "' . lowerCase2($_REQUEST['prezimeGlumca']) . '")');
    }
    if (!empty($_REQUEST['glumacSpol'])) {
        array_push($filter, 'contains(' . lowerCase('@glumacSpol') . ', "' . lowerCase2($_REQUEST['glumacSpol']) . '")');
    }
    if (!empty($_REQUEST['datumRodjenjaGlumca'])) {
        $datumRodjenjaFormatiran = formatDate($_REQUEST['datumRodjenjaGlumca']);
        array_push($filter, 'contains(' . lowerCase('datum_rodjenja') . ', "' . lowerCase2($datumRodjenjaFormatiran) . '")');
    }
    if ($_REQUEST['nacionalnostGlumca'] != "-") {
        array_push($filter, 'contains(' . lowerCase('@nacionalnost') . ', "' . lowerCase2($_REQUEST['nacionalnostGlumca']) . '")');
    }
    if (!empty($_REQUEST['brojDjece'])) {
        array_push($filter, 'contains(' . lowerCase('@brojDjece') . ', "' . lowerCase2($_REQUEST['brojDjece']) . '")');
    }
    if (!empty($_REQUEST['filmografija'])) {
        array_push($filter, 'contains(' . lowerCase('filmografija') . ', "' . lowerCase2($_REQUEST['filmografija']) . '")');
    }
    if (!empty($_REQUEST['zanimanje'])) {
        $zanimanja = array();
        foreach ($_REQUEST['zanimanje'] as $item) {
            array_push($zanimanja, "zanimanje/@imeZanimanja, " . $item . "");
        }
        if (!empty($zanimanja)) {
            $zanimanja = implode(" or ", $zanimanja);
            @$zanimanja = "contains(" . $zanimanja . ")";
        }
        array_push($filter, $zanimanja);
    }

    $filter = implode(" and ", $filter);

    if (empty($filter)) return "/podaci/glumac";

    return "/podaci/glumac[" . $filter . "]";
}

function lowerCase($string)
{
    return "translate(" . $string . ",'ABCDEFGHIJKLLJMNNJOPQRSTUVWXYZŠĐČĆŽ' ,  'abcdefghijklljmnnjopqrstuvwxyzšđčćž' )";
}

function lowerCase2($string)
{
    return mb_strtolower($string, "UTF-8");
}

function formatDate($date)
{
    $dateArray = explode("-", $date);
    $year = $dateArray[0];
    $month = $dateArray[1];
    $day = $dateArray[2];

    $monthNoun = "";

    if ($month == 1) $monthNoun = "January";
    elseif ($month == 2) $monthNoun = "February";
    elseif ($month == 3) $monthNoun = "March";
    elseif ($month == 4) $monthNoun = "April";
    elseif ($month == 5) $monthNoun = "May";
    else if ($month == 6) $monthNoun = "June";
    elseif ($month == 7) $monthNoun = "July";
    elseif ($month == 8) $monthNoun = "August";
    elseif ($month == 9) $monthNoun = "September";
    elseif ($month == 10) $monthNoun = "October";
    elseif ($month == 11) $monthNoun = "November";
    elseif ($month == 12) $monthNoun = "December";

    return $monthNoun . " " . $day . ", " . $year;
}


//LV 4 ================================================================================================================


function getJson($actorWikiPageTitle)
{
    $actorWikiPageTitle = str_replace(' ', '_', $actorWikiPageTitle);
    $actorJsonFile = "https://en.wikipedia.org/api/rest_v1/page/summary/" . $actorWikiPageTitle;
    $actorJsonFile = file_get_contents($actorJsonFile);
    $actorJsonFile = JSON_decode($actorJsonFile, true);

    return $actorJsonFile;
}

function getImgUrl($actorJsonFile)
{
    $actorImgUrl = $actorJsonFile['originalimage']['source'];
    return $actorImgUrl;
}

function getRestCoordinates($actorJsonFile)
{
    $coordinates = array();
    if (array_key_exists('coordinates', $actorJsonFile) == true) {
        $coordinates = $actorJsonFile['coordinates'];
    } else {
        $coordinates['lat'] = "Nije definirano";
        $coordinates['lon'] = "Nije definirano";
    }
    return $coordinates;
}

function getSummary($actorJsonFile)
{
    $summary = $actorJsonFile['extract'];

    //shorten the summary if too long
    if (strlen($summary) > 1500) {
        $summary = substr($summary, 0, strrpos($summary, ". ", 1500 - strlen($summary)) + 1);
    }

    return $summary;
}

function getMediaWikiCoordinates($glumacWikiPageTitle)
{
    //get the actor's residence
    $req = 'http://en.wikipedia.org/w/api.php?&format=json&action=query&prop=revisions&rvprop=content&titles=';
    $glumacWikiPageTitle = str_replace(' ', '_', $glumacWikiPageTitle);
    $req = $req . $glumacWikiPageTitle;
    $dataInfoBox = file_get_contents($req);
    $residence = substr($dataInfoBox, strpos($dataInfoBox, "residence"));
    $residence = substr($residence, 0, strpos($residence, "\\n"));
    $residence = substr($residence, strpos($residence, "[[") + 2);
    $residence = substr($residence, 0, strpos($residence, "]],"));
    $residence = str_replace(" ", "_", $residence);
    $residenceLocation = 'http://en.wikipedia.org/w/api.php?format=json&action=query&prop=coordinates&titles=' . $residence;
    $dataLocation = file_get_contents($residenceLocation);

    //get the page id and check for redirects
    $pageId = substr($dataLocation, strpos($dataLocation, "pageid") + 8);
    $pageId = substr($pageId, 0, strpos($pageId, "ns") - 2);
    $req = 'http://en.wikipedia.org/w/api.php?action=query&format=json&pageids=' . $pageId . '&redirects&prop=redirects';
    $dataLocation = file_get_contents($req);
    //get the id of the page that is being redirected to
    $pageId = substr($dataLocation, strpos($dataLocation, "pageid") + 8);
    $pageId = substr($pageId, 0, strpos($pageId, "ns") - 2);

    //get the coordinates
    $residenceLocation = 'http://en.wikipedia.org/w/api.php?format=json&action=query&prop=coordinates&pageids=' . $pageId;
    $dataLocation = file_get_contents($residenceLocation);
    $coordinatesLat = substr($dataLocation, strpos($dataLocation, "lat") + 5);
    $coordinatesLat = substr($coordinatesLat, 0, strpos($coordinatesLat, "lon") - 2);
    $coordinatesLon = substr($dataLocation, strpos($dataLocation, "lon") + 5);
    $coordinatesLon = substr($coordinatesLon, 0, strpos($coordinatesLon, "primary") - 2);

    $coordinates = array();

    if (strpos($coordinatesLat, 'missing') == true
        || strpos($coordinatesLon, 'missing') == true) {
        array_push($coordinates, "Nije definirano", "Nije definirano");

    } else {
        array_push($coordinates, $coordinatesLat, $coordinatesLon);
    }
    return $coordinates;
}

function getNominatimCoordinates($name)
{

    $req = 'http://en.wikipedia.org/w/api.php?format=json&action=query&prop=revisions&rvprop=content&rvsection=0&titles=' . $name;
    $dataLocation = file_get_contents($req);


    $address = substr($dataLocation, strpos($dataLocation, "location            ="));
    $address = substr($address, strpos($address, "=") + 2);
    $address = explode("[[", $address);

    $street = $address[0] . $address[1];
    $street = str_replace("<br />", "", $street);
    $street = substr($street, 0, strpos($street, "]]"));
    $street = str_replace(" ", "%20", $street);


    $city = $address[2];
    $city = substr($city, 0, strpos($city, "]]"));
    $city = str_replace(" ", "%20", $city);
    echo $city;

    $country = $address[3];
    $country = substr($country, 0, strpos($country, "]]"));
    $country = str_replace(" ", "%20", $country);


// Create a user agent
    $opts = array('http' => array('header' => "User-Agent: Ivan Cindric\r\n"));
    $context = stream_context_create($opts);

//get the results
    $nominatimSearch = 'http://nominatim.openstreetmap.org/search?q=' . $street . "%20" . $city . "%20" . $country . '&format=xml';

    $coordsXml = file_get_contents($nominatimSearch, false, $context);
    $coordsXmlSimple = simplexml_load_string($coordsXml);

    $lat = $coordsXmlSimple->children()[0]->attributes()[5];
    $lat = (string)$lat;

    $lon = $coordsXmlSimple->children()[0]->attributes()[6];
    $lon = (string)$lon;

    $coordinates = array();
    array_push($coordinates, $lat, $lon);

    return $coordinates;
}

//LV.6 ================================================================================================================

function getFilmography($actorName)
{
    $key = '074999a0bb91837e32f380d8c7856a26';

    $actorName = str_replace(" ", "%20", $actorName);

    $actorJsonFile = "https://api.themoviedb.org/3/search/person?api_key=" . $key . "&language=en-US&query=" . $actorName . "&page=1&include_adult=false" . $key;
    $actorJsonFile = file_get_contents($actorJsonFile);
    $actorJsonFile = JSON_decode($actorJsonFile, true);

    $knownFor = $actorJsonFile['results'];
    $knownFor = $knownFor['0'];
    $knownFor = $knownFor['known_for'];

    $movieNames = "";

    foreach ($knownFor as $movie) {
        $movieNames = $movieNames . ", " . $movie['original_title'];
    }
    $movieNames = substr($movieNames, 1);

    return $movieNames;

}

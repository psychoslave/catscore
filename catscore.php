<?php
$exlimit = 20;
$cmlimit = 50;

function getResponse($query){
    $url = 'https://en.wikipedia.org/w/api.php' . $query;
    $user_agent = 'catscore/0.0.1 '
        . '(http://tools.wmflabs.org/catscore/; msg at wikimedia dot fr)';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function extractIntro($titles)
{
    global $exlimit;
    $query = "?action=query&prop=extracts&exintro&format=json"
        . "&exlimit=" . $exlimit
        . "&titles=" . urlencode($titles);
    $response = json_decode(getResponse($query));
    //echo $query;
    //var_dump($response);
    return $response->query->pages;
}

function categoryMembers($category)
{
    global $cmlimit;
    $query = '?action=query&list=categorymembers&format=json'
        . '&cmlimit=' . $cmlimit
        . '&cmtitle=Category:' . urlencode($category);
    $response = json_decode( getResponse($query) ) ;
    return $response->query->categorymembers;
}

function score($summary)
{
    $length = strlen($summary);
    if($length ==  0)
        return 0;
    return 100./$length;
}

function concatTitles($pages){
    $titles = "";
    foreach($pages as $page){
        $titles .= $page->title . '|';
    }
    return rtrim($titles, "|,");
}

function fetchSummaries($pages){
    global $exlimit;
    $untreated = count($pages);
    $summaries = array();
    for($i = 0; $i < count($pages); $i += $exlimit){
        //var_dump($pages);
        $page_buffer = array_slice ( $pages, $i, $exlimit);
        $titles = concatTitles($page_buffer);
        //echo $titles;
        $summaries = array_merge($summaries, (array) extractIntro($titles));
    }
    return $summaries;
}

$category = "Philosophy";
$pages = categoryMembers($category);
$summaries = fetchSummaries($pages);

//var_dump( $summaries);
$scores = array();
foreach($summaries as $page){
    $scores[$page->title] = score($page->extract);
}
asort($scores);
var_dump( $scores);
/*
 */

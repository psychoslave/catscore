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
        . '&cmnamespace=0' // only include main namespace, ignore subcategories
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
		// extract summuries by slices according to the limitation on how many
        // for entry can be fetched at a time by bots
        $page_buffer = array_slice ( $pages, $i, $exlimit);
        $titles = concatTitles($page_buffer);
        //echo $titles;
        $summaries = array_merge($summaries, (array) extractIntro($titles));
    }
    return $summaries;
}

function getScores($category){
	$pages = categoryMembers($category);
	$summaries = fetchSummaries($pages);

	//var_dump( $summaries);
	$scores = array();
	foreach($summaries as $page){
		$scores[$page->title] = score($page->extract);
	}
	asort($scores);
	return $scores;
}

// Section for flow control
if(!isset($_GET["cat"]))
	$category = 'Philosophy'; // User didn't ask anything, but hey…
else
	$category = $_GET["cat"]; // User parameter 
$category = filter_var($category, FILTER_SANITIZE_SPECIAL_CHARS); // avoid  '<script>alert("evil code here!")</script>'
// $category = escapeshellarg($category); // Should be safe like that

$scores = getScores($category);
//var_dump($scores);
// Section for HTML output formating
?>
<html>
<body>
<h1>Summary scoring for some articles in the <?php echo $category ?> category</h1>
<table>
<tr><th>Title</th><th>Score</th>
<?php
foreach($scores as $title => $value)
        echo '<tr><td>' . $title . '</td><td>' . $value . "</td></tr>\n";
?>
</table>
</body>
</html>


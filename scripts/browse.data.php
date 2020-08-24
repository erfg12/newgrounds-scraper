<?PHP
require_once("functions.inc.php");
isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : $type = 'movies';
if ($type == 'games')
	$urlrun = 'https://www.newgrounds.com/games/browse/tag/html5';
else
	$urlrun = 'https://www.newgrounds.com/' . $type . '/browse/sort/date';

if (isset($_REQUEST['category'])) $urlrun .= '/genre/' . $_REQUEST['category'];
if (isset($_REQUEST['page'])) $urlrun .= '/page/' . $_REQUEST['page'];
$tspider = new tagSpider();
$adult_filter = 0;
if (isset($_REQUEST['a']))
	$adult_filter = $_REQUEST['a'];

$cleanPass = filter_var_array($_REQUEST['pass'], FILTER_SANITIZE_STRING);
$cleanUser = filter_var_array($_REQUEST['user'], FILTER_SANITIZE_STRING);
$tspider->fetchPage($urlrun, $cleanUser, $cleanPass, 1, 1, 1, $adult_filter);

if ($type == 'movies' || $type == 'games') {
	$stag = '<div class="item-details">';
	$etag = '</div>
			</div>';
} else if ($type == 'audio') {
	$stag = '<li><div class="audio-wrapper">';
	$etag = '</li>';
} else if ($type == 'art') {
	$stag = '<a href="//www.newgrounds.com/art/view/';
	$etag = '</a>';
}
$dataArray['browse_content'] = array();
$i = 0;
foreach ($tspider->parse_array($stag, $etag) as $data) {
	// link
	preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', 			$data, $a);
	// thumbnail
	preg_match_all('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', 			$data, $img);
	// title
	if ($type == 'art')
		$title['alt'][0] = fatf(substr(strrchr($a['href'][0], "/"), 1));
	else
		preg_match_all('/<img[^>]+alt=([\'"])(?<alt>.+?)\1[^>]*>/i', 		$data, $title);
	// rating
	//if ($type == 'art')
	//	preg_match	('/rated-([^"]*) /i', 				 					$data, $rating);
	//else
	preg_match('/rated-([^"]*)"/i', 				 					$data, $rating);
	// author
	if ($type == 'audio')
		preg_match('/<strong>([^"]*)<\/strong>/i', 						$data, $author);
	else if ($type == 'art')
		preg_match('/<span>by ([^"]*)<\/span>/i', 		$data, $author);
	else
		$author[1] = '';
	// description
	preg_match('/<div class="item-icon-hover">([^"]*)<div/i', 		$data, $desc);
	// genre
	if ($type == 'movies' || $type == 'games')
		preg_match('/<div class="item-genre">([^"]*)<\/div>/i', 			$data, $genre);
	else
		preg_match('/<div class="detail-genre">([^"]*)<\/div>/i', 		$data, $genre);

	if ($type == 'art') {
		$id = explode("/", cleanImg($img['src'][0]));
		$id = $id[5];
		$id = str_replace('.jpg', '', $id);
		$id = str_replace('.png', '', $id);
	} else {
		$id = explode("/", $a['href'][0]);
		$id = $id[5];
	}

	$theData = array('id' => $id, 'link' => 'https:' . $a['href'][0], 'image' => cleanImg($img['src'][0]), 'title' => $title['alt'][0], 'description' => cleanString($desc[1]), 'rating' => $rating[1], 'author' => $author[1], 'genre' => cleanString($genre[1]));
	if ($type != 'art') {
		if (empty($title['alt'][0])) continue; // empty data can appear
	} else {
		if ($i == 42) break;
	}
	array_push($dataArray['browse_content'], $theData);
	$i++;
}

if (isset($_GET['raw'])) {
	$stag6 = '<html>';
	$etag6 = '</html>';
	$dataArray['raw'] = array();
	foreach ($tspider->parse_array($stag6, $etag6) as $data) {
		array_push($dataArray['raw'], $data);
	}
}
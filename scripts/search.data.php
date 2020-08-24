<?PHP
require_once ("functions.inc.php");
$urlrun = 'https://www.newgrounds.com/search/conduct/'.$_REQUEST['type'].'?terms='.$_REQUEST['search'].'&page='.$_REQUEST['page'];
//8-10-2018 - https://www.newgrounds.com/search/conduct/movies?terms=test&suitabilities=etm
$tspider = new tagSpider();
$tspider->fetchPage($urlrun);

if ($_REQUEST['type'] == 'movies' || $_REQUEST['type'] == 'games'){
	$stag='<a href="//www.newgrounds.com/portal/view/';
	$etag='</li>';
} else if ($_REQUEST['type'] == 'audio'){
	$stag='<div class="audio-wrapper">';
	$etag='</li>';
} else if ($_REQUEST['type'] == 'art'){
	$stag='<a href="//www.newgrounds.com/art/view/';
	$etag='</a>';
}

$dataArray['results'] = array();
$linkarray = $tspider->parse_array($stag, $etag);
foreach ($linkarray as $data) {
	preg_match_all		('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', 			$data, $a);
	preg_match_all		('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', 			$data, $img);
	preg_match_all		("'<h4>(.*?)</h4>'si", 									$data, $name);
	if ($_REQUEST['type'] == 'art'){
		preg_match			('/<span>By ([^"]*)<\/span>/i', 						$data, $author);
	} else {
		preg_match			('/<strong>([^"]*)<\/strong>/i', 						$data, $author);
	}
	preg_match			('/rated-([^"]*)"/i', 									$data, $rated);
	preg_match_all		("'<div class=\"detail-description\">(.*?)</div>'si", 	$data, $desc);
	$theData = array('link'=>'https:'.$a['href'][0], 'image'=>cleanImg($img['src'][0]), 'name'=>strip_tags($name[1][0]), 'author'=>$author[1], 'description'=>trim(strip_tags($desc[1][0])), 'rated'=>$rated[1]);
	array_push($dataArray['results'], $theData);
}

if (isset($_GET['raw'])){
	$stag6='<html>';
	$etag6='</html>';
	$dataArray['raw'] = array();
	foreach ($tspider->parse_array($stag6, $etag6) as $data) {
		array_push($dataArray['raw'], $data);
	}
}
?>
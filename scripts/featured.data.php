<?PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("functions.inc.php");
$urlrun = 'https://www.newgrounds.com/';
$tspider = new tagSpider();
$tspider->fetchPage($urlrun);

//// ART
$stag = '<a href="https://www.newgrounds.com/art/view/';
$etag = '</a>';
$featuredArray['featured_art'] = array();
foreach ($tspider->parse_array($stag, $etag) as $data) {
	if (!strstr($data, "item-portalitem-art-small")) continue;
	preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', 			$data, $a);
	preg_match_all('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', 			$data, $img);
	$title 			= substr(strrchr($a['href'][0], "/"), 1);
	preg_match('/<div class="rated-([^"]*) /i', 				 		$data, $rating);
	preg_match('/<div class="item-details">by ([^"]*)<\/div>/i', 		$data, $author);
	$id = explode("/", cleanImg($img['src'][0]));
	$id = $id[5];
	$id = str_replace('.jpg', '', $id);
	$id = str_replace('.png', '', $id);
	$id = str_replace('.gif', '', $id);
	$theData = array('id' => $id, 'link' => $a['href'][0], 'image' => cleanImg($img['src'][0]), 'title' => fatf($title), 'description' => '', 'rating' => $rating[1], 'author' => $author[1], 'genre' => '');
	array_push($featuredArray['featured_art'], $theData);
}

//// AUDIO
$stag2 = '<div class="item-audiosubmission-small">.*<a href="https://www.newgrounds.com/audio/listen/';
$etag2 = '</div></li>';
$featuredArray['featured_audio'] = array();
foreach ($tspider->parse_array($stag2, $etag2) as $data) {
	preg_match('/href="([^"]*)"/i', 									$data, $url);
	preg_match_all('/<img[^>]+alt=([\'"])(?<alt>.+?)\1[^>]*>/i', 			$data, $img2);
	preg_match('/src="([^"]*)"/i', 									$data, $thumb);
	preg_match('/<strong>([^"]*)<\/strong>/i', 						$data, $author);
	preg_match('/<div class="detail-genre">([^"]*)<\/div>/i', 		$data, $genre);
	$id = explode("/", $url[1]);
	$id = $id[5];

	$u = $url[1];
	$im = cleanImg($thumb[1]);
	$t = $img2['alt'][0];
	$a = $author[1];
	$g = cleanString(str_replace('Song ', '', $genre[1]));

	$theData = array('id' => $id, 'link' => $u, 'image' => $im, 'title' => $t, 'description' => '', 'rating' => '', 'author' => $a, 'genre' => $g);
	array_push($featuredArray['featured_audio'], $theData);
}

//// GAMES
/*$stag3 = '<div class="span-1 align-center">.*<a href="https://www.newgrounds.com/portal/view/';
$etag3 = '</a>';
$featuredArray['featured_games'] = array();
$i = 0;
foreach ($tspider->parse_array($stag3, $etag3) as $data) {
	$i++;
	if ($i <= 12) continue; // videos come before games
	preg_match('/href="([^"]*)"/i', 									$data, $url);
	preg_match_all('/<img[^>]+alt=([\'"])(?<alt>.+?)\1[^>]*>/i', 			$data, $img2);
	preg_match('/img src="([^"]*)"/i', 								$data, $src);
	preg_match('/<div class="item-icon-hover">([^"]*)<div/i', 		$data, $desc);
	preg_match('/rated-([^"]*)"/i', 				 					$data, $rating);
	preg_match_all("'<span>(.*?)</span>'si", 								$data, $author);
	preg_match('/<div class="item-genre">([^"]*)<\/div>/i', 			$data, $genre);
	$id = explode("/", $url[1]);
	$id = $id[5];

	$u = $url[1];
	$cm = cleanImg($src[1]);
	$t = $img2['alt'][0];
	$d = cleanString($desc[1]);
	$r = $rating[1];
	$a = $author[1][1];
	$g = cleanString($genre[1]);

	$theData = array('id' => $id, 'link' => $u, 'image' => $cm, 'title' => $t, 'description' => $d, 'rating' => $r, 'author' => $a, 'genre' => $g);
	array_push($featuredArray['featured_games'], $theData);
	if ($i == 24) break; // there is other advertised games/videos after
}

//// SERIES & COLLECTIONS
$stag4 = '<div class="collection-fp"><a href="https://www.newgrounds.com/collection/';
$etag4 = '</a>';
$featuredArray['featured_series'] = array();
foreach ($tspider->parse_array($stag4, $etag4) as $data) {
	preg_match('/src="([^"]*)"/i', 									$data, $thumb);
	preg_match('/<h4>([^"]*)<\/h4>/i', 								$data, $title);
	preg_match('/href="([^"]*)"/i', 									$data, $url);
	preg_match('/<div class="detail-description">([^"]*)<\/div>/i', 	$data, $desc);
	$theData = array('link' => $url[1], 'image' => cleanImg($thumb[1]), 'title' => $title[1], 'description' => cleanString($desc[1]), 'rating' => '', 'author' => '', 'genre' => '');
	array_push($featuredArray['featured_series'], $theData);
}*/

//// VIDEOS
$stag5 = '<div class="span-1 align-center">.*<a href="https://www.newgrounds.com/portal/view/';
$etag5 = '</a>';
$featuredArray['featured_videos'] = array();
$i = 0;
foreach ($tspider->parse_array($stag5, $etag5) as $data) {
	$i++;
	preg_match('/href="([^"]*)"/i', 									$data, $url);
	preg_match_all('/<img[^>]+alt=([\'"])(?<alt>.+?)\1[^>]*>/i', 			$data, $img2);
	preg_match('/img src="([^"]*)"/i', 								$data, $src);
	preg_match("'<div class=\"item-icon-hover\">(.*?)<div'si", 		$data, $desc);
	preg_match('/rated-([^"]*)"/i', 				 					$data, $rating);
	preg_match_all("'<span>(.*?)</span>'si", 								$data, $author);
	preg_match('/<div class="item-genre">([^"]*)<\/div>/i', 			$data, $genre);
	$id = explode("/", cleanImg($src[1]));
	$id = $id[4];
	$id = str_replace('.jpg', '', $id);
	$id = str_replace('.png', '', $id);
	$id = str_replace('flash_', '', $id);
	$theData = array('id' => $id, 'link' => $url[1], 'image' => cleanImg($src[1]), 'title' => $img2['alt'][0], 'description' => cleanString($desc[1]), 'rating' => $rating[1], 'author' => $author[1][1], 'genre' => cleanString($genre[1]));
	array_push($featuredArray['featured_videos'], $theData);
	if ($i == 12) break; // games are after videos
}

if (isset($_GET['raw'])) {
	$stag6 = '<html lang="en">';
	$etag6 = '</html>';
	$dataArray['raw'] = array();
	foreach ($tspider->parse_array($stag6, $etag6) as $data) {
		array_push($dataArray['raw'], $data);
	}
}
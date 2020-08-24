<?PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("functions.inc.php");
$urlrun = 'https://' . $_REQUEST['user'] . '.newgrounds.com/';
$tspider = new tagSpider();
$tspider->fetchPage($urlrun);

$stag = 'id="user-header-icon"';
$etag = '</a>';

$stag2 = 'id="user_links"';
$etag2 = '</ul>';

$stag3 = '<span class="user-stats-small">';
$etag3 = 'id="sortable_sections"';

$dataArray['avatar'] = array();
$linkarray = $tspider->parse_array($stag, $etag);
foreach ($linkarray as $data) {
	preg_match('/background-image: url\(([^"]*)\)"/i', 				$data, $icon);
	$theData = array('icon' => cleanImg('https:' . str_replace("'", '', $icon[1])));
	array_push($dataArray['avatar'], $theData);
}

$dataArray['information'] = array();
$linkarray = $tspider->parse_array($stag3, $etag3);
foreach ($linkarray as $data) {
	preg_match("'Level:(.*?)</dd>'si", 									$data, $level);
	preg_match("'Exp Points:(.*?)</dd>'si", 								$data, $exp_points);
	preg_match("'Exp Rank:(.*?)</dd>'si", 								$data, $exp_rank);
	preg_match("'Vote Power:(.*?)</dd>'si", 								$data, $vote_power);
	preg_match("'Rank:(.*?)</dd>'si", 									$data, $rank);
	preg_match("'Global Rank:(.*?)</dd>'si", 								$data, $global_rank);
	preg_match("'Blams:(.*?)</dd>'si", 									$data, $blams);
	preg_match("'Saves:(.*?)</dd>'si", 									$data, $saves);
	preg_match("'Whistle:(.*?)</dd>'si", 									$data, $whistle);
	preg_match("'Trophies:(.*?)</dd>'si", 								$data, $trophies);
	preg_match("'Medals:(.*?)</dd>'si", 									$data, $medals);
	preg_match("'Supporter:(.*?)</dd>'si", 								$data, $supporter);
	$trophy = '';
	if (array_key_exists(1, $trophies))
		$trophy = $trophies[1];
	$supp = '';
	if (array_key_exists(1, $supporter))
		$supp = $supporter[1];
	$medalsVar = '';
	if (array_key_exists(1, $medals))
		$medalsVar = $medals[1];
	$theData = array(
		'level' => trim(strip_tags($level[1])),
		'exp_points' => trim(strip_tags($exp_points[1])),
		'exp_rank' => trim(strip_tags($exp_rank[1])),
		'trophies' => trim(strip_tags($trophy)),
		'vote_power' => trim(strip_tags($vote_power[1])),
		'rank' => trim(strip_tags($rank[1])),
		'global_rank' => trim(strip_tags($global_rank[1])),
		'whistle' => trim(strip_tags($whistle[1])),
		'blams' => trim(strip_tags($blams[1])),
		'saves' => trim(strip_tags($saves[1])),
		'medals' => trim(strip_tags($medalsVar)),
		'supporter' => trim(strip_tags($supp))
	);
	array_push($dataArray['information'], $theData);
}

$dataArray['websites'] = array();
$linkarray = $tspider->parse_array($stag2, $etag2);
foreach ($linkarray as $data) {
	preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', 			$data, $a);
	preg_match_all("'<strong class=\"link\">(.*?)</strong>'si", 			$data, $name);
	for ($i = 0; $i < count($a[1]); $i++) {
		$theData = array('link' => $a['href'][$i], 'name' => $name[1][$i]);
		array_push($dataArray['websites'], $theData);
	}
}

if (isset($_GET['raw'])) {
	$stag6 = '<html>';
	$etag6 = '</html>';
	$dataArray['raw'] = array();
	foreach ($tspider->parse_array($stag6, $etag6) as $data) {
		array_push($dataArray['raw'], $data);
	}
}
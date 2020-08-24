<?PHP
require_once ("functions.inc.php");
$tspider = new tagSpider();

// main video, game, audio and art source
if (strstr($_REQUEST['url'],'/art/view/')){
	$stag='<div class="image" itemprop="aggregateRating"';
	$etag='</div>';
} else if (strstr($_REQUEST['url'],'/portal/view/')){
	$stag='"url":"';
	$etag='"';
} else {
	$stag='"filename":"';
	$etag='"';
}

// author(s), contributors and 3rd party resources used
	$stag2 = '<div class="item-user">';
	$etag2 = '</div>.*</div>';

// author's comments (aka description)
	$stag3='id="author_comments"';
	$etag3='</div>';

// the people have spoken (aka comments)
	$stag4='class="pod-body review"';
	$etag4='jshint';

if (strstr($_REQUEST['url'],'/audio/listen/')){
	$stag5='CDATA';
	$etag5=']>';
}

// more information
	$stag6='follow-user';
	$etag6='<div class="pod-head">';

  function checkFileExists($url) {
    /*echo 'reading';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($retcode == 200)
      return true;
    else
      return false;*/
      return true;
  }

if (strstr($_REQUEST['url'],'/portal/view/')) //use mobile
	$tspider->fetchMobilePage($_REQUEST['url']);
else
	$tspider->fetchPage($_REQUEST['url']);
$dataArray['content'] = array();
foreach ($tspider->parse_array($stag, $etag) as $data) {
	if (strstr($_REQUEST['url'],'/portal/view/')){
		preg_match		('/"url":"([^"]*)"/i', 								$data, $media);
		if (strstr($media[1],"newage")) continue;

		// get proper MP4 video
		$theURL = ""; // if we cant find MP4, give nothing
		$ogURL = cleanImg(str_replace("\\",'',$media[1]));
		$path_info = pathinfo($ogURL);
		if ($path_info['extension'] == "zip") {
			$theURL = $ogURL;
		} else {
			if ($path_info['extension'] != "mp4" && checkFileExists(str_replace($path_info['extension'],'mp4',$ogURL)) == true){ // given URL isnt MP4, check if an MP4 file exists
				$theURL = str_replace($path_info['extension'],'mp4',$ogURL);
			} else {
				$theURL = $ogURL;
			}
		}
		$theData = array('media'=>$theURL);
	}
	if (strstr($_REQUEST['url'],'/audio/listen/')){
		preg_match		('/"filename":"([^"]*)"/i', 						$data, $media);
		$theData = array('media'=>cleanImg(str_replace("\\",'',$media[1])));
	}
	if (strstr($_REQUEST['url'],'/art/view/')) {
		preg_match		('/src="([^"]*)"/i', 								$data, $media);
		$theData = array('media'=>cleanImg($media[1]));
	}
	array_push($dataArray['content'], $theData);
}

if (strstr($_REQUEST['url'],'/portal/view/')) //restore non-mobile
	$tspider->fetchPage($_REQUEST['url']);
$dataArray['authors'] = array();
$linkarray = $tspider->parse_array($stag2, $etag2);
foreach ($linkarray as $data) {
	preg_match_all		('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', 			$data, $a);
	preg_match_all		('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', 			$data, $img);
	preg_match_all		('/<img[^>]+alt=([\'"])(?<alt>.+?)\1[^>]*>/i', 			$data, $name);
	for($i=0; $i < count($img[1]); $i++) {
		if ($a[1] == '')
			continue;
		$theData = array('link'=>'https:'.$a['href'][$i*3], 'image'=>cleanImg('https:'.$img['src'][$i]), 'name'=>$name['alt'][$i]);
		array_push($dataArray['authors'], $theData);
	}
}

$dataArray['description'] = array();
foreach ($tspider->parse_array($stag3, $etag3) as $data) {
	preg_match		("'id=\"author_comments\">(.*?)</div>'si", 					$data, $desc);
	$theData = array('details'=>cleanString2($desc[1]));
	array_push($dataArray['description'], $theData);
}

$dataArray['comments'] = array();
foreach ($tspider->parse_array($stag4, $etag4) as $data) {
	preg_match_all		('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', 			$data, $a);
	preg_match_all		('/<img[^>]+src=([\'"])(?<src>.+?)\1[^>]*>/i', 			$data, $img);
	preg_match_all		('/<img[^>]+alt=([\'"])(?<alt>.+?)\1[^>]*>/i', 			$data, $name);
	preg_match		('/Score: ([^"]*)\//i', 				 					$data, $rating);
	preg_match		("'<div class=\"review-body(.*?)</div>'si", 									$data, $content);
	$theData = array('link'=>'https:'.$a['href'][0], 'image'=>cleanImg($img['src'][0]), 'name'=>$name['alt'][0], 'rating'=>$rating[1], 'content'=>strip_tags(cleanString($content[0])));
	array_push($dataArray['comments'], $theData);
}

if (isset($stag5)) {
	$dataArray['audio_details'] = array();
	foreach ($tspider->parse_array($stag5, $etag5) as $data) {
		preg_match		('/\"name\":\"(.*?)\"/i', 				 				$data, $name);
		preg_match		('/\"artist\":\"(.*?)\"/i', 				 			$data, $artist);
		preg_match		('/\"icon\":\"(.*?)\"/i', 				 				$data, $icon);
		if ($name[1] != null && $name[1] != "NeWaGe") {
			$theData = array('name'=>urldecode($name[1]), 'artist'=>$artist[1], 'icon'=>cleanImg(str_replace('\\','',$icon[1])));
			array_push($dataArray['audio_details'], $theData);
		}
	}
}

$dataArray['information'] = array();
foreach ($tspider->parse_array($stag6, $etag6) as $data) {
	preg_match		("'id=\"score_number\">(.*?)</span>'si", 				 	$data, $score);
	preg_match		("'<dt>Uploaded</dt>(.*?)</dd>'si", 				 		$data, $uploaded);
	if (strstr($_REQUEST['url'],'/art/view/') || strstr($_REQUEST['url'],'/portal/view/')) // art, videos or games
		preg_match		("'<dt>Views</dt>(.*?)</dd>'si", 				 		$data, $views);
	else //audio
		preg_match		("'<dt>Listens</dt>(.*?)</dd>'si", 				 		$data, $views);
	if (strstr($_REQUEST['url'],'/art/view/'))
		preg_match		("'<dt>Category</dt>(.*?)</dd>'si", 				 	$data, $genre); //art
	else
		preg_match		("'<dt>Genre</dt>(.*?)</dd>'si", 				 		$data, $genre);
	$cu = strip_tags($uploaded[1]);
	$cu = str_replace(array("\r\n", "\r", "\n"),'',$cu);
	$cv = cleanString($views[1]);
	$theData = array('score'=>strip_tags($score[1]), 'uploaded'=>strtotime($cu), 'genre'=>cleanString($genre[1]), 'views'=>str_replace(',','',$cv));
	array_push($dataArray['information'], $theData);
}

$tspider->fetchMobilePage($_REQUEST['url']);
if (isset($_GET['raw'])){
	$stag6='<html';
	$etag6='</html>';
	$dataArray['raw'] = array();
	foreach ($tspider->parse_array($stag6, $etag6) as $data) {
		array_push($dataArray['raw'], $data);
	}
}
?>

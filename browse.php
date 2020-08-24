<?PHP
// optional variables: (can be given by _GET, _COOKIE or _POST)
// type: movies, audio, art, games.
// user: your newgrounds username
// pass: your newgrounds password
// category: The content categories. There are lots available, check newgrounds.com for a list.
// page: page to browse to
// e: Everyone rating. Boolean. (enabled by default)
// t: Teen rating. Boolean. (enabled by default)
// m: Mature rating. Boolean. (enabled by default)
// a: Adult rating. Boolean. (disabled by default, requires valid username and password)
require_once("scripts/browse.data.php");
header('Content-Type: application/json; charset=utf-8');

$json = json_encode($dataArray, JSON_PRETTY_PRINT);
echo $json;
?>
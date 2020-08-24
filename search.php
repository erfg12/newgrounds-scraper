<?PHP
// give variables search and type by _GET, _COOKIE or _POST
// search: use a url safe variable
// page: page # of search browsing
// type: movies, audio, art, games
require_once("scripts/search.data.php");
header('Content-Type: application/json; charset=utf-8');

$json = json_encode($dataArray, JSON_PRETTY_PRINT);
echo $json;
?>
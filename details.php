<?PHP
// requires a url to parse
// give variable url by _GET, _COOKIE or _POST

require_once("scripts/details.data.php");
header('Content-Type: application/json; charset=utf-8');

$json = json_encode($dataArray, JSON_PRETTY_PRINT);
echo $json;
?>
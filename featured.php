<?PHP
// for debugging
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once("scripts/featured.data.php");
header('Content-Type: application/json; charset=utf-8');

$featuredJson = json_encode($featuredArray, JSON_PRETTY_PRINT);
echo $featuredJson;
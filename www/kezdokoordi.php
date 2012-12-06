<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
?>
/*{"kezdokoordi":<?
$kezdo_bolygo=mysql2row('select x,y from bolygok where tulaj='.$uid.' order by nev limit 1');
if ($kezdo_bolygo) {
	echo '['.$kezdo_bolygo['x'].','.$kezdo_bolygo['y'].']';
} else {
	echo '[0,0]';
}
?>}*/
<?
mysql_close($mysql_csatlakozas);
?>
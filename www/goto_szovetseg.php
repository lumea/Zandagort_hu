<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$er=mysql_query('select id from szovetsegek where nev="'.sanitstr($_REQUEST['q']).'"');
$aux=mysql_fetch_array($er);
if ($aux) {
?>
/*{"letezik":1,"id":<?=$aux[0];?>}*/
<?
} else {
?>
/*{"letezik":0}*/
<?
}

?>
<? mysql_close($mysql_csatlakozas);?>
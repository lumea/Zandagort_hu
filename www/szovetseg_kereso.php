<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

if (substr($_REQUEST['nev'],0,1)=='#') {
	$er=mysql_query('select id from szovetsegek where id='.((int)substr($_REQUEST['nev'],1))) or hiba(__FILE__,__LINE__,mysql_error());
} else {
	$er=mysql_query('select id from szovetsegek where nev="'.sanitstr($_REQUEST['nev']).'"') or hiba(__FILE__,__LINE__,mysql_error());
}
$aux=mysql_fetch_array($er);
if ($aux[0]>0) {
?>
/*{"id":<?=$aux[0];?>}*/
<?
}

?>
<? mysql_close($mysql_csatlakozas);?>
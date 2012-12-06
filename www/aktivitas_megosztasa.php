<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$er=mysql_query('select id from userek where nev="'.sanitstr($_REQUEST['nev']).'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);

mysql_query('insert ignore into aktivitas_megosztas (ki,kivel) values('.$uid.','.$aux[0].')') or hiba(__FILE__,__LINE__,mysql_error());
kilep();
?>
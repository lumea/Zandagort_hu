<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$ismert) kilep();

$aux=mysql2row('select id from userek where nev="'.sanitstr($_REQUEST['nev']).'"');
mysql_query('update userek set helyettes_id='.((int)$aux[0]).' where id='.$uid);

kilep();
?>
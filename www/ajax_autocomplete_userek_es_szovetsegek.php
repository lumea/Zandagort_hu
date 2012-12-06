<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
?>
/*{"nevek":<?
echo mysql2jsonarray('
select nev from userek where nev like "'.sanitstr($_REQUEST['x']).'%"
union all
select nev from szovetsegek where nev like "'.sanitstr($_REQUEST['x']).'%"
order by nev limit 10');
?>}*/
<?

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>
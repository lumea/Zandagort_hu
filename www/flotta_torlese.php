<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['flotta_id']=(int)$_REQUEST['flotta_id'];

$er=mysql_query('select * from flottak where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
$flotta=mysql_fetch_array($er);
if (!$flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($flotta['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);

$vannak_e_idegen_reszflottai=sanitint(mysql2num('select coalesce(sum(rfh.hp*h.ar),0) from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$flotta['id'].' and user_id!='.$uid));
if ($vannak_e_idegen_reszflottai>0) {
	kilep($lang[$lang_lang]['kisphpk']['Másnak is van része a flottában.']);
}

insert_into_hajo_transzfer_log($flotta['tulaj'],$flotta['tulaj_szov'],$flotta['id'],0,0,0,$flotta['egyenertek']);

flotta_torles($_REQUEST['flotta_id']);

kilep();
?>
<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['flotta_id']=(int)$_REQUEST['flotta_id'];

$er2=mysql_query('select * from flottak where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
$flotta=mysql_fetch_array($er2);
if (!$flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($flotta['tulaj']!=$uid && $flotta['kezelo']!=$uid && ($flotta['kozos']!=1 || $jogaim[5]!=1 || $flotta['tulaj_szov']!=$adataim['tulaj_szov'])) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);
if ($flotta['statusz']!=STATUSZ_ALLOMAS || $flotta['bolygo']==0) kilep($lang[$lang_lang]['kisphpk']['Csak állomásozó flottát lehet visszavonni.']);

$vannak_e_idegen_reszflottai=sanitint(mysql2num('select coalesce(sum(rfh.hp*h.ar),0) from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$flotta['id'].' and user_id!='.$uid));
if ($vannak_e_idegen_reszflottai>0) {
	kilep($lang[$lang_lang]['kisphpk']['Másnak is van része a flottában.']);
}

$er2=mysql_query('select * from bolygok where id='.$flotta['bolygo']) or hiba(__FILE__,__LINE__,mysql_error());
$bolygo=mysql_fetch_array($er2);
if (!$bolygo) kilep($lang[$lang_lang]['kisphpk']['Csak állomásozó flottát lehet visszavonni.']);
if ($bolygo['tulaj']!=$flotta['tulaj']) kilep($lang[$lang_lang]['kisphpk']['Csak saját bolygóra lehet flottát visszavonni.']);

//LOCK ELEJE
mysql_query('lock tables bolygo_eroforras write, flotta_hajo read');
$er=mysql_query('select * from flotta_hajo where flotta_id='.$flotta['id'].' and hajo_id>0') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	$ossz_hp=$aux['ossz_hp'];
	if ($aux['hajo_id']!=HAJO_TIPUS_SZONDA) {//szondanal nem szamit a moral
		$moral=$aux['moral'];
		if ($moral<10000) {//indulo moral: 100% -> 10000
			$ossz_hp=$ossz_hp/10000*$moral;
		}
	}
	mysql_query('update bolygo_eroforras set db=db+'.$ossz_hp.' where bolygo_id='.$bolygo['id'].' and eroforras_id='.$aux['hajo_id']) or hiba(__FILE__,__LINE__,mysql_error());
}
mysql_query('unlock tables');
//LOCK VEGE
flotta_torles($flotta['id']);


insert_into_hajo_transzfer_log($flotta['tulaj'],$flotta['tulaj_szov'],$flotta['id'],$bolygo['tulaj'],$bolygo['tulaj_szov'],-$bolygo['id'],$flotta['egyenertek']);

kilep('***'.$bolygo['id']);
?>